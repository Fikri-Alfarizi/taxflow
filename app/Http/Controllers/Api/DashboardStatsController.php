<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pajak;
use App\Models\Dokumen;
use Illuminate\Http\Request;

class DashboardStatsController extends Controller
{
    public function index()
    {
        // Cache for 60 seconds for "near real-time" but high performance
        return cache()->remember('api_dashboard_stats', 60, function () {
            $now = now();
            
            $counts = Pajak::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            return [
                'total' => array_sum($counts),
                'diproses' => $counts['diproses'] ?? 0,
                'selesai' => $counts['selesai'] ?? 0,
                'terlambat' => $counts['terlambat'] ?? 0,
                'mendekati' => Pajak::where('status', '!=', 'selesai')
                    ->where('tanggal_jatuh_tempo', '>=', $now)
                    ->where('tanggal_jatuh_tempo', '<=', $now->copy()->addDays(7))
                    ->count(),
                'dokumen' => Dokumen::count(),
                'last_sync' => cache('last_tax_sync', 'Belum pernah'),
                'updated_at' => now()->format('H:i:s'),
            ];
        });
    }
}
