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
        $query = Pajak::with('user');

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

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['nama_perusahaan', 'jenis_pajak', 'periode', 'tanggal_jatuh_tempo', 'status', 'created_at'];
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
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
            'npwp' => 'nullable|string|max:50',
            'alamat_lengkap' => 'nullable|string',
            'nama_pic' => 'nullable|string|max:255',
            'kontak_pic' => 'nullable|string|max:50',
            'email_pic' => 'nullable|email|max:255',
            'kpp_pratama' => 'nullable|string|max:255',
            'kategori_usaha' => 'nullable|string|max:255',
            'id_transaksi_source' => 'nullable|string|max:100',
            'jenis_pajak' => 'required|string|max:255',
            'periode' => 'required|string|max:50',
            'tanggal_input' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'tanggal_jatuh_tempo' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'keterangan' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'diproses';

        $pajak = Pajak::create($data);

        // Tambah log awal di monitorings
        $pajak->monitorings()->create([
            'user_id' => auth()->id(),
            'status_proses' => 'diproses',
            'catatan' => 'Data pajak baru dibuat.',
        ]);

        return redirect()->route('pajak.index')->with('success', 'Data Pajak berhasil ditambahkan.');
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
            'npwp' => 'nullable|string|max:50',
            'alamat_lengkap' => 'nullable|string',
            'nama_pic' => 'nullable|string|max:255',
            'kontak_pic' => 'nullable|string|max:50',
            'email_pic' => 'nullable|email|max:255',
            'kpp_pratama' => 'nullable|string|max:255',
            'kategori_usaha' => 'nullable|string|max:255',
            'id_transaksi_source' => 'nullable|string|max:100',
            'jenis_pajak' => 'required|string|max:255',
            'periode' => 'required|string|max:50',
            'tanggal_input' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'tanggal_jatuh_tempo' => 'required|date|date_format:Y-m-d|before:9999-12-31',
            'keterangan' => 'nullable|string',
        ]);

        $pajak->update($data);

        return redirect()->route('pajak.index')->with('success', 'Data Pajak berhasil diperbarui.');
    }

    public function export()
    {
        $pajaks = Pajak::with('user')->get();
        $csvFileName = 'data_pajak_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Perusahaan', 'Jenis Pajak', 'Periode', 'Tanggal Input', 'Jatuh Tempo', 'Sisa Hari', 'Status', 'Petugas Penginput'];

        $callback = function() use($pajaks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($pajaks as $p) {
                fputcsv($file, [
                    $p->nama_perusahaan,
                    $p->jenis_pajak,
                    $p->periode,
                    $p->tanggal_input,
                    $p->tanggal_jatuh_tempo,
                    $p->sisa_hari_text,
                    $p->status,
                    $p->user->name ?? 'Sistem'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
