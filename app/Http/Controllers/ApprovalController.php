<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use App\Models\Dokumen;
use App\Models\CatatanPerbaikan;
use App\Models\LaporanPajak;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovalController extends Controller
{
    /**
     * Admin Dashboard untuk Approval Workflow
     */
    public function dashboard(Request $request)
    {
        $query = Pajak::with(['user', 'verifiedBy', 'approvedBy']);

        // Universal Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_perusahaan', 'like', "%{$s}%")
                  ->orWhere('jenis_pajak', 'like', "%{$s}%")
                  ->orWhere('periode', 'like', "%{$s}%");
            });
        }

        // Status Verifikasi Filter
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Status Validasi Filter
        if ($request->filled('status_validasi')) {
            $query->where('status_validasi', $request->status_validasi);
        }

        // Status Approval Filter
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }

        // Jenis Pajak Filter
        if ($request->filled('jenis_pajak')) {
            $query->where('jenis_pajak', $request->jenis_pajak);
        }

        // Staff Filter
        if ($request->filled('staff_id')) {
            $query->where('user_id', $request->staff_id);
        }

        // Hitung statistik pending (Tetap hitung global untuk counter atas)
        $pendingVerifikasi = Pajak::where('status_verifikasi', 'pending')->count();
        $pendingValidasi = Dokumen::where('status_validasi', 'pending')->count();
        $pendingApproval = Pajak::where('status_validasi', 'valid')
                               ->where('status_approval', 'pending')
                               ->count();
        $totalPajak = Pajak::count();

        // Ambil data untuk dropdown filter
        $jenisPajakList = Pajak::select('jenis_pajak')->distinct()->pluck('jenis_pajak');
        $staffList = \App\Models\User::whereHas('pajaks')->select('id', 'name')->get();

        $pajaks = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('approval.dashboard', compact(
            'pendingVerifikasi',
            'pendingValidasi',
            'pendingApproval',
            'totalPajak',
            'pajaks',
            'jenisPajakList',
            'staffList'
        ));
    }

    /**
     * UC-007: Verifikasi Data Pajak
     * Admin memeriksa data pajak yang dikirim oleh staff
     */
    public function verifikasiData(Request $request, $id)
    {
        $pajak = Pajak::findOrFail($id);

        // Validasi workflow state - hanya bisa verifikasi jika status masih pending
        if ($pajak->status_verifikasi !== 'pending') {
            return redirect()->back()->with('error', 'Data pajak sudah diverifikasi sebelumnya.');
        }

        // Validasi request
        $request->validate([
            'status_verifikasi' => 'required|in:verified,needs_revision',
            'catatan_perbaikan' => 'nullable|string|max:1000',
        ]);

        // Update status verifikasi
        $pajak->update([
            'status_verifikasi' => $request->status_verifikasi,
            'verified_by' => Auth::id(),
            'tanggal_verifikasi' => now(),
        ]);

        // Jika perlu revisi, buat catatan perbaikan (sesuai flowmap)
        if ($request->status_verifikasi === 'needs_revision') {
            $catatan = $request->catatan_perbaikan ?: 'Perlu revisi data tanpa catatan spesifik.';
            CatatanPerbaikan::create([
                'pajak_id' => $pajak->id,
                'created_by' => Auth::id(),
                'catatan_perbaikan' => $catatan,
                'status' => 'belum_diperbaiki',
            ]);

            // Reset status validasi dan approval
            $pajak->update([
                'status_validasi' => 'pending',
                'status_approval' => 'pending',
            ]);

            // Create monitoring log
            $pajak->monitorings()->create([
                'user_id' => Auth::id(),
                'status_proses' => 'revisi',
                'catatan' => 'Admin meminta revisi data: ' . $catatan,
                'tanggal_update' => now(),
            ]);

            $message = 'Data pajak dikembalikan untuk diperbaiki dengan catatan revisi.';
        } else {
            // Create monitoring log
            $pajak->monitorings()->create([
                'user_id' => Auth::id(),
                'status_proses' => 'verified',
                'catatan' => 'Data pajak berhasil diverifikasi oleh admin.',
                'tanggal_update' => now(),
            ]);

            $message = 'Data pajak berhasil diverifikasi dan siap untuk validasi dokumen.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * UC-008: Validasi Dokumen
     * Admin memvalidasi dokumen yang telah diunggah
     */
    public function validasiDokumen(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $pajak = $dokumen->pajak;

        // Validasi workflow state - hanya bisa validasi jika sudah diverifikasi
        if ($pajak->status_verifikasi !== 'verified') {
            return redirect()->back()->with('error', 'Data pajak harus diverifikasi terlebih dahulu sebelum validasi dokumen.');
        }

        // Validasi request
        $request->validate([
            'status_validasi' => 'required|in:valid,invalid',
            'keterangan_validasi' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf($request->status_validasi === 'invalid')
            ],
        ]);

        // Update status validasi dokumen
        $dokumen->update([
            'status_validasi' => $request->status_validasi,
            'validated_by' => Auth::id(),
            'tanggal_validasi' => now(),
            'keterangan_validasi' => $request->keterangan_validasi,
        ]);

        // Update status validasi di pajak jika semua dokumen sudah divalidasi
        $pajak->load('dokumens'); // Reload relationship
        $allDocumentsValidated = $pajak->dokumens()->where('status_validasi', 'pending')->count() === 0;

        if ($allDocumentsValidated) {
            $hasInvalidDocuments = $pajak->dokumens()->where('status_validasi', 'invalid')->count() > 0;
            $pajak->update([
                'status_validasi' => $hasInvalidDocuments ? 'invalid' : 'valid',
            ]);

            // Create monitoring log
            $statusMessage = $hasInvalidDocuments ? 'beberapa dokumen tidak valid' : 'semua dokumen valid';
            $pajak->monitorings()->create([
                'user_id' => Auth::id(),
                'status_proses' => $hasInvalidDocuments ? 'invalid' : 'valid',
                'catatan' => "Validasi dokumen selesai: {$statusMessage}.",
                'tanggal_update' => now(),
            ]);
        }

        $message = $request->status_validasi === 'valid'
            ? 'Dokumen berhasil divalidasi.'
            : 'Dokumen ditandai sebagai tidak valid: ' . $request->keterangan_validasi;

        return redirect()->back()->with('success', $message);
    }

    /**
     * UC-009: Setujui/Tolak Proses Pajak
     * Admin memberikan keputusan akhir terhadap proses pajak
     */
    public function approveReject(Request $request, $id)
    {
        $pajak = Pajak::findOrFail($id);

        // Validasi workflow state - hanya bisa approve jika sudah divalidasi
        if ($pajak->status_validasi !== 'valid') {
            return redirect()->back()->with('error', 'Semua dokumen harus divalidasi terlebih dahulu sebelum approval.');
        }

        if ($pajak->status_approval !== 'pending') {
            return redirect()->back()->with('error', 'Proses pajak sudah diputuskan sebelumnya.');
        }

        // Validasi request
        $request->validate([
            'status_approval' => 'required|in:approved,rejected',
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf($request->status_approval === 'rejected')
            ],
        ]);

        // Update status approval
        $pajak->update([
            'status_approval' => $request->status_approval,
            'approved_by' => Auth::id(),
            'tanggal_approval' => now(),
        ]);

        // Jika disetujui, update status utama menjadi selesai
        if ($request->status_approval === 'approved') {
            $pajak->update(['status' => 'selesai']);

            // Create monitoring log
            $pajak->monitorings()->create([
                'user_id' => Auth::id(),
                'status_proses' => 'selesai',
                'catatan' => 'Proses pajak disetujui dan selesai.',
                'tanggal_update' => now(),
            ]);

            $message = 'Proses pajak berhasil disetujui dan ditandai sebagai selesai.';
        } else {
            // Jika ditolak, update status utama
            $pajak->update(['status' => 'ditolak']);

            // Create monitoring log
            $pajak->monitorings()->create([
                'user_id' => Auth::id(),
                'status_proses' => 'ditolak',
                'catatan' => 'Proses pajak ditolak: ' . $request->keterangan,
                'tanggal_update' => now(),
            ]);

            $message = 'Proses pajak ditolak: ' . $request->keterangan;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * UC-011: Generate Laporan Pajak
     * Admin membuat laporan pajak dalam format PDF/Excel
     */
    public function generateLaporan(Request $request, $id)
    {
        $pajak = Pajak::with(['user', 'verifiedBy', 'approvedBy', 'dokumens', 'monitorings', 'catatanPerbaikans'])->findOrFail($id);

        // Validasi workflow state - hanya bisa generate jika sudah approved
        if ($pajak->status_approval !== 'approved') {
            return redirect()->back()->with('error', 'Laporan hanya bisa dibuat untuk proses pajak yang sudah disetujui.');
        }

        // Validasi request
        $request->validate([
            'jenis_laporan' => 'required|in:PDF,Excel',
            'periode_laporan' => 'required|date',
        ]);

        // Generate filename
        $filename = 'laporan-pajak-' . $pajak->id . '-' . now()->format('Y-m-d-H-i-s') . '.' . strtolower($request->jenis_laporan);

        // Ensure storage directory exists
        Storage::makeDirectory('public/laporan-pajak');

        if ($request->jenis_laporan === 'PDF') {
            // Generate PDF
            $pdf = Pdf::loadView('laporan.pajak', compact('pajak'));
            $content = $pdf->output();

            // Store file
            Storage::put('public/laporan-pajak/' . $filename, $content);
        } else {
            // Generate Excel (simplified - in real app use Laravel Excel)
            $content = $this->generateExcelContent($pajak);
            Storage::put('public/laporan-pajak/' . $filename, $content);
        }

        // Save to database
        LaporanPajak::create([
            'pajak_id' => $pajak->id,
            'generated_by' => Auth::id(),
            'periode_laporan' => $request->periode_laporan,
            'jenis_laporan' => $request->jenis_laporan,
            'file_laporan' => $filename,
            'konten_laporan' => $request->jenis_laporan === 'PDF' ? null : $content,
        ]);

        // Create monitoring log
        $pajak->monitorings()->create([
            'user_id' => Auth::id(),
            'status_proses' => 'laporan_dibuat',
            'catatan' => "Laporan pajak {$request->jenis_laporan} berhasil dibuat.",
            'tanggal_update' => now(),
        ]);

        return redirect()->back()->with('success', "Laporan pajak {$request->jenis_laporan} berhasil dibuat dan disimpan.");
    }

    /**
     * Helper method untuk generate Excel content (simplified)
     */
    private function generateExcelContent($pajak)
    {
        $content = "Laporan Pajak\n";
        $content .= "Nama Perusahaan: {$pajak->nama_perusahaan}\n";
        $content .= "Jenis Pajak: {$pajak->jenis_pajak}\n";
        $content .= "Periode: {$pajak->periode}\n";
        $content .= "Status: {$pajak->status}\n";
        $content .= "Generated at: " . now()->format('Y-m-d H:i:s') . "\n";

        return $content;
    }

    /**
     * Mark catatan perbaikan as completed (for staff)
     */
    public function selesaiPerbaikan(Request $request, $id)
    {
        $catatan = CatatanPerbaikan::findOrFail($id);
        $pajak = $catatan->pajak;

        // Only allow if user is the owner of the pajak
        if ($pajak->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $catatan->update([
            'status' => 'selesai_diperbaiki',
            'tanggal_perbaikan' => now(),
        ]);

        // Create monitoring log
        $pajak->monitorings()->create([
            'user_id' => Auth::id(),
            'status_proses' => 'revisi_selesai',
            'catatan' => 'Staff telah menyelesaikan perbaikan data sesuai catatan admin.',
            'tanggal_update' => now(),
        ]);

        return redirect()->back()->with('success', 'Perbaikan data telah ditandai sebagai selesai.');
    }

    /**
     * Get dokumen list for validation modal
     */
    public function getDokumenList($pajakId)
    {
        $pajak = Pajak::findOrFail($pajakId);
        $dokumens = $pajak->dokumens;

        return response()->json($dokumens);
    }

    /**
     * Simulasi randomize approval status untuk testing
     */
    public function simulasiRandomize(Request $request)
    {
        try {
            $pajaks = Pajak::all();
            $admin = User::where('role', 'admin')->first() ?? User::first();
            
            $updated = 0;
            $updatedStatuses = [];
            
            foreach ($pajaks as $pajak) {
                $rand = rand(1, 100);
                
                // Default: All Pending
                $status_v = 'pending';
                $status_va = 'pending';
                $status_a = 'pending';
                $tanggal_v = null;
                $tanggal_va = null;
                $tanggal_a = null;
                $status_utama = $pajak->status;

                if ($rand > 30) { // 70% chance to be at least verified
                    $status_v = 'verified';
                    $tanggal_v = now()->subDays(rand(1, 10));
                    
                    if ($rand > 60) { // 40% chance to be validated (if verified)
                        $status_va = 'valid';
                        $tanggal_va = $tanggal_v->copy()->addHours(rand(1, 24));
                        
                        if ($rand > 85) { // 15% chance to be approved (if valid)
                            $status_a = 'approved';
                            $tanggal_a = $tanggal_va->copy()->addHours(rand(1, 24));
                            $status_utama = 'selesai';
                        } elseif ($rand > 80) { // 5% chance to be rejected
                            $status_a = 'rejected';
                            $tanggal_a = $tanggal_va->copy()->addHours(rand(1, 24));
                            $status_utama = 'ditolak';
                        }
                    } elseif ($rand > 55) { // 5% chance to be invalid
                        $status_va = 'invalid';
                        $tanggal_va = $tanggal_v->copy()->addHours(rand(1, 24));
                    }
                } elseif ($rand > 25) { // 5% chance to be revision
                    $status_v = 'needs_revision';
                    $tanggal_v = now()->subDays(rand(1, 5));
                }

                $pajak->update([
                    'status_verifikasi' => $status_v,
                    'status_validasi' => $status_va,
                    'status_approval' => $status_a,
                    'verified_by' => $status_v !== 'pending' ? ($admin->id ?? 1) : null,
                    'approved_by' => $status_a === 'approved' ? ($admin->id ?? 1) : null,
                    'tanggal_verifikasi' => $tanggal_v,
                    'tanggal_validasi' => $tanggal_va,
                    'tanggal_approval' => $tanggal_a,
                    'status' => $status_utama,
                ]);
                
                $updated++;
                $updatedStatuses[] = [
                    'id' => $pajak->id,
                    'nama' => $pajak->nama_perusahaan,
                    'verif' => $status_v,
                    'valid' => $status_va,
                    'approve' => $status_a
                ];
            }

            // Log untuk debugging
            \Log::info("Simulasi randomize selesai: {$updated} data di-update", $updatedStatuses);

            return redirect()->back()->with('success', "Simulasi selesai! {$updated} data telah di-randomize.");
        } catch (\Exception $e) {
            \Log::error("Error simulasi: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
