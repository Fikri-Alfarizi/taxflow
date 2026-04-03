<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Pajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokumen::with('pajak');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama_dokumen', 'like', "%{$s}%")
                  ->orWhereHas('pajak', function($pq) use ($s) {
                      $pq->where('nama_perusahaan', 'like', "%{$s}%");
                  });
            });
        }

        $dokumens = $query->latest()->paginate(10)->withQueryString();

        return view('dokumen.index', compact('dokumens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pajak_id' => 'required|exists:pajaks,id',
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|mimes:pdf,xls,xlsx,png,jpg,jpeg|max:5120',
        ]);

        try {
            $file = $request->file('file_dokumen');
            $filePath = $file->store('dokumen_pajak', 'public');

            Dokumen::create([
                'pajak_id' => $request->pajak_id,
                'nama_dokumen' => $request->nama_dokumen,
                'file_dokumen' => $filePath,
                'ukuran_file' => $file->getSize(),
            ]);

            \Log::info("File uploaded successfully: {$filePath} for Pajak ID: {$request->pajak_id}");

            return back()
                ->with('success', 'Aset Digital berhasil diamankan ke dalam Vault.')
                ->with('active_tab', 'vault');
        } catch (\Exception $e) {
            \Log::error("File upload failed: " . $e->getMessage());
            return back()
                ->with('error', 'Gagal mengunggah aset: ' . $e->getMessage())
                ->with('active_tab', 'vault');
        }
    }

    public function destroy(Dokumen $dokumen)
    {
        if (!$dokumen->exists) {
            return back()->with('error', 'Aset tidak ditemukan dalam Vault atau ID tidak valid.');
        }

        try {
            if ($dokumen->file_dokumen && Storage::disk('public')->exists($dokumen->file_dokumen)) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }
            $dokumen->delete();

            return back()
                ->with('success', 'Aset Digital berhasil dihapus dari Vault.')
                ->with('active_tab', 'vault');
        } catch (\Exception $e) {
            \Log::error("Delete failure: " . $e->getMessage());
            return back()
                ->with('error', 'Gagal menghapus aset: ' . $e->getMessage())
                ->with('active_tab', 'vault');
        }
    }
}
