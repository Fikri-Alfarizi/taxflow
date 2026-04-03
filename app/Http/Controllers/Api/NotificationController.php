<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pajak;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $now = now();
        
        // Fetch urgent alerts (Overdue or Deadline <= 3 days)
        $urgentPajaks = Pajak::where('status', '!=', 'selesai')
            ->where('tanggal_jatuh_tempo', '<=', $now->copy()->addDays(3))
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->take(5)
            ->get();

        $notifications = $urgentPajaks->map(function ($pajak) {
            $days = $pajak->sisa_hari;
            $type = $days < 0 ? 'danger' : ($days <= 3 ? 'warning' : 'info');
            $message = $days < 0 
                ? "Terlambat " . abs($days) . " hari" 
                : ($days == 0 ? "Jatuh tempo hari ini" : "$days hari lagi");

            return [
                'id' => $pajak->id,
                'title' => $pajak->nama_perusahaan,
                'subtitle' => $pajak->jenis_pajak,
                'message' => $message,
                'type' => $type,
                'url' => route('pajak.show', $pajak->id),
                'time_human' => \Carbon\Carbon::parse($pajak->tanggal_jatuh_tempo)->diffForHumans(),
            ];
        });

        return [
            'count' => $notifications->count(),
            'items' => $notifications,
        ];
    }
}
