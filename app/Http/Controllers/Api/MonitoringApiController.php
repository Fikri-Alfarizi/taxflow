<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Monitoring;
use Illuminate\Http\Request;
 
class MonitoringApiController extends Controller
{
    /**
     * Fetch latest monitoring logs for real-time dashboard.
     */
    public function latest(Request $request)
    {
        $afterId = $request->get('after_id', 0);
        
        $updates = Monitoring::with(['pajak', 'user'])
            ->where('id', '>', $afterId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'audit_id' => '#LOG-' . str_pad($log->id, 5, '0', STR_PAD_LEFT),
                    'timestamp' => \Carbon\Carbon::parse($log->tanggal_update)->format('d/m/y H:i'),
                    'perusahaan' => $log->pajak->nama_perusahaan ?? 'DATA_PURGED',
                    'pajak_url' => $log->pajak ? route('pajak.show', $log->pajak->id) : '#',
                    'jenis_pajak' => $log->pajak->jenis_pajak ?? '-',
                    'periode' => $log->pajak->periode ?? '-',
                    'status' => $log->status_proses,
                    'status_label' => $log->status_proses == 'diproses' ? 'PROSES' : ($log->status_proses == 'selesai' ? 'STABIL' : 'KRITIS'),
                    'status_class' => $log->status_proses == 'diproses' ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-500 border-amber-100 dark:border-amber-900' : ($log->status_proses == 'selesai' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 border-emerald-100 dark:border-emerald-900' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-500 border-rose-100 dark:border-rose-900'),
                    'catatan' => $log->catatan ?: '-',
                    'user_name' => $log->user->name ?? 'System',
                    'user_initial' => $log->user ? substr($log->user->name, 0, 1) : 'S',
                ];
            });
 
        return response()->json([
            'updates' => $updates,
            'latest_id' => $updates->max('id') ?? $afterId
        ]);
    }
}
