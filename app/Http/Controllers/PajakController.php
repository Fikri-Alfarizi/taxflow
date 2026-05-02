<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use App\Services\TaxApiService;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public function sync(TaxApiService $service)
    {
        $results = $service->syncFromExternalApi();

        return back()->with('success', "Sinkronisasi berhasil! {$results['created']} data baru ditambahkan, {$results['updated']} diperbarui.");
    }

    public function index(Request $request)
    {
        $query = Pajak::with(['user', 'catatanPerbaikans' => function($q) {
            $q->where('status', 'belum_diperbaiki')->latest();
        }]);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_perusahaan', 'like', "%{$s}%")
                  ->orWhere('jenis_pajak', 'like', "%{$s}%")
                  ->orWhere('periode', 'like', "%{$s}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis pajak
        if ($request->filled('jenis_pajak')) {
            $query->where('jenis_pajak', $request->jenis_pajak);
        }

        // Filter range tanggal jatuh tempo
        if ($request->filled('dari_tanggal')) {
            $query->where('tanggal_jatuh_tempo', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->where('tanggal_jatuh_tempo', '<=', $request->sampai_tanggal);
        }

        // Sorting with priority for items needing repair
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['nama_perusahaan', 'jenis_pajak', 'periode', 'tanggal_jatuh_tempo', 'status', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            // Priority ordering: items with pending repairs first, then by requested sort
            $query->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM catatan_perbaikans cp
                        WHERE cp.pajak_id = pajaks.id
                        AND cp.status = 'belum_diperbaiki'
                    ) THEN 1
                    ELSE 0
                END DESC
            ")->orderBy($sortBy, $sortOrder);
        } else {
            // Default: priority for items needing repair, then latest
            $query->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM catatan_perbaikans cp
                        WHERE cp.pajak_id = pajaks.id
                        AND cp.status = 'belum_diperbaiki'
                    ) THEN 1
                    ELSE 0
                END DESC
            ")->latest();
        }

        $pajaks = $query->paginate(10)->withQueryString();

        // Get unique jenis_pajak for filter dropdown
        $jenisPajakList = Pajak::select('jenis_pajak')->distinct()->pluck('jenis_pajak');

        return view('pajak.index', compact('pajaks', 'jenisPajakList'));
    }

    public function create()
    {
        return view('pajak.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'status_perusahaan' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'nib' => 'nullable|string|max:50',
            'no_akta' => 'nullable|string|max:100',
            'sk_kemenkumham' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|numeric|digits:4|between:1901,2155',
            'alamat_lengkap' => 'nullable|string|max:1000',
            'nama_pic' => 'nullable|string|max:255',
            'jabatan_pic' => 'nullable|string|max:255',
            'kontak_pic' => 'nullable|string|max:50',
            'email_pic' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'kpp_pratama' => 'nullable|string|max:255',
            'kategori_usaha' => 'nullable|string|max:255',
            'id_transaksi_source' => 'nullable|string|max:100',
            'jenis_pajak' => 'required|string|max:255',
            'jenis_pajak_rincian' => 'nullable|string|max:255',
            'periode' => 'required|string|max:50',
            'tanggal_input' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'tanggal_jatuh_tempo' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'diproses';
        
        // Validasi dan normalisasi data
        if (empty($data['jenis_pajak']) || strlen($data['jenis_pajak']) < 2) {
            return back()->withInput()->with('error', 'Jenis Pajak harus dipilih dengan benar.');
        }
        if (empty($data['jenis_pajak_rincian']) || strlen($data['jenis_pajak_rincian']) < 2) {
            return back()->withInput()->with('error', 'Jenis Pajak Rincian harus dipilih dengan benar.');
        }
        if (empty($data['periode']) || strlen($data['periode']) < 4) {
            return back()->withInput()->with('error', 'Periode harus diisi dengan format yang benar (contoh: Agustus 2026).');
        }

        try {
            $pajak = Pajak::create($data);

            // Tambah log awal di monitorings
            $pajak->monitorings()->create([
                'user_id' => auth()->id(),
                'status_proses' => 'diproses',
                'catatan' => 'Data pajak baru dibuat.',
            ]);

            return redirect()->route('pajak.index')->with('success', 'Data Pajak berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(Pajak $pajak)
    {
        $pajak->load(['user', 'monitorings' => function($q) {
            $q->latest();
        }, 'dokumens']);

        return view('pajak.show', compact('pajak'));
    }

    public function edit(Pajak $pajak)
    {
        return view('pajak.edit', compact('pajak'));
    }

    public function update(Request $request, Pajak $pajak)
    {
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'status_perusahaan' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'nib' => 'nullable|string|max:50',
            'no_akta' => 'nullable|string|max:100',
            'sk_kemenkumham' => 'nullable|string|max:100',
            'tahun_berdiri' => 'nullable|numeric|digits:4|between:1901,2155',
            'alamat_lengkap' => 'nullable|string|max:1000',
            'nama_pic' => 'nullable|string|max:255',
            'jabatan_pic' => 'nullable|string|max:255',
            'kontak_pic' => 'nullable|string|max:50',
            'email_pic' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'kpp_pratama' => 'nullable|string|max:255',
            'kategori_usaha' => 'nullable|string|max:255',
            'id_transaksi_source' => 'nullable|string|max:100',
            'jenis_pajak' => 'required|string|max:255',
            'jenis_pajak_rincian' => 'nullable|string|max:255',
            'periode' => 'required|string|max:50',
            'tanggal_input' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'tanggal_jatuh_tempo' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $pajak->update($data);

        return redirect()->route('pajak.index')->with('success', 'Data Pajak berhasil diperbarui.');
    }

    public function export(Request $request)
    {
        $query = Pajak::with('user');

        // Filter dari modal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_jatuh_tempo', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_jatuh_tempo', '<=', $request->sampai_tanggal);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pajaks = $query->latest()->get();
        $tipe_ekspor = $request->get('tipe_ekspor', 'excel');
        $filename = 'TaxFlow_Data_Pajak_' . date('Ymd_His');

        if ($tipe_ekspor === 'excel') {
            return response(view('pajak.export_document', compact('pajaks', 'tipe_ekspor')))
                ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');
        } else {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pajak.export_document', compact('pajaks', 'tipe_ekspor'))
                     ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }
    }

    public function destroy(Pajak $pajak)
    {
        try {
            // Hapus dokumen terkait dan filenya
            foreach ($pajak->dokumens as $dokumen) {
                if ($dokumen->file_dokumen && \Illuminate\Support\Facades\Storage::disk('public')->exists($dokumen->file_dokumen)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($dokumen->file_dokumen);
                }
                $dokumen->delete();
            }

            // Hapus relasi lainnya
            $pajak->monitorings()->delete();
            $pajak->catatanPerbaikans()->delete();
            $pajak->laporanPajaks()->delete();

            $pajak->delete();

            return redirect()->route('pajak.index')->with('success', 'Data Pajak berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            \Log::error("Gagal menghapus data pajak: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data pajak: ' . $e->getMessage());
        }
    }
}
