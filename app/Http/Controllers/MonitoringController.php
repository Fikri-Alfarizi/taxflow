<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use App\Models\Monitoring;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Monitoring::with(['pajak', 'user']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('pajak', function($q) use ($s) {
                $q->where('nama_perusahaan', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status_proses')) {
            $query->where('status_proses', $request->status_proses);
        }

        $monitorings = $query->latest()->paginate(10)->withQueryString();

        return view('monitoring.index', compact('monitorings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pajak_id' => 'required|exists:pajaks,id',
            'status_proses' => 'required|in:diproses,selesai,terlambat',
            'catatan' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        Monitoring::create($data);

        // Update status master di Pajak
        $pajak = Pajak::find($data['pajak_id']);
        $pajak->status = $data['status_proses'];
        $pajak->save();

        return back()
            ->with('success', 'Status progress berhasil diperbarui.')
            ->with('active_tab', 'audit');
    }

    public function destroy(Monitoring $monitoring)
    {
        $monitoring->delete();
        return back()
            ->with('success', 'Catatan progress berhasil dihapus.')
            ->with('active_tab', 'audit');
    }
}
