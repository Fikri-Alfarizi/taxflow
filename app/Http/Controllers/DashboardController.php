<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pajak;
use App\Models\Monitoring;
use App\Models\Dokumen;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache the dashboard data for 10 minutes to resolve lag
        $dashboardData = cache()->remember('dashboard_stats', 600, function () {
            $now = now();
            
            // Get all status counts in ONE query instead of 6
            $counts = Pajak::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            $stats = [
                'total' => array_sum($counts),
                'diproses' => $counts['diproses'] ?? 0,
                'selesai' => $counts['selesai'] ?? 0,
                'terlambat' => $counts['terlambat'] ?? 0,
                'mendekati' => Pajak::where('status', '!=', 'selesai')
                    ->where('tanggal_jatuh_tempo', '>=', $now)
                    ->where('tanggal_jatuh_tempo', '<=', $now->copy()->addDays(7))
                    ->count(),
                'dokumen' => Dokumen::count(),
            ];

            // Warning List (H-3) - still real-time for accuracy or short cache
            $warningPajak = Pajak::where('status', '!=', 'selesai')
                ->where('tanggal_jatuh_tempo', '<=', $now->copy()->addDays(3))
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->take(10)
                ->get();

            // Trend Data (Single query optimization)
            $trend = ['labels' => [], 'data' => []];
            for ($i = 5; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $trend['labels'][] = $month->format('M Y');
                $count = Pajak::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
                $trend['data'][] = $count;
            }

            return compact('stats', 'warningPajak', 'trend');
        });

        $stats = $dashboardData['stats'];
        $warningPajak = $dashboardData['warningPajak'];
        $trend = $dashboardData['trend'];

        // Recent Activities (Not cached, always live)
        $activities = Monitoring::with(['pajak', 'user'])
            ->latest()
            ->take(8)
            ->get();

        $lastSync = cache('last_tax_sync', 'Belum pernah');

        return view('dashboard', compact('stats', 'warningPajak', 'trend', 'activities', 'lastSync'));
    }
}
