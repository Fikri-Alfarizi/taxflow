<?php

namespace App\Http\Controllers;

use App\Models\Pajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        // Aggregate Completion Rates per Tax Type (High-Density Analytics)
        $taxTypeStats = Pajak::select('jenis_pajak', 
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "selesai" then 1 else 0 end) as completed'),
                DB::raw('sum(case when status = "terlambat" then 1 else 0 end) as overdue')
            )
            ->groupBy('jenis_pajak')
            ->get();

        // Monthly Trend Analytics (Last 6 Months)
        $monthlyTrend = Pajak::select(
                DB::raw("DATE_FORMAT(tanggal_jatuh_tempo, '%M %Y') as month"),
                DB::raw("count(*) as total")
            )
            ->where('tanggal_jatuh_tempo', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // High-Priority Companies (Those with most overdue)
        $topDefaulters = Pajak::select('nama_perusahaan', 'logo_url', DB::raw('count(*) as overdue_count'))
            ->where('status', 'terlambat')
            ->groupBy('nama_perusahaan', 'logo_url')
            ->orderBy('overdue_count', 'desc')
            ->limit(5)
            ->get();

        $overallStats = [
            'total_records' => Pajak::count(),
            'avg_completion' => Pajak::where('status', 'selesai')->count() / max(1, Pajak::count()) * 100,
            'critical_records' => Pajak::where('status', 'terlambat')->count()
        ];

        return view('laporan.index', compact('taxTypeStats', 'monthlyTrend', 'topDefaulters', 'overallStats'));
    }

    public function exportPdf()
    {
        // Aggregate Completion Rates per Tax Type
        $taxTypeStats = Pajak::select('jenis_pajak', 
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "selesai" then 1 else 0 end) as completed'),
                DB::raw('sum(case when status = "terlambat" then 1 else 0 end) as overdue')
            )
            ->groupBy('jenis_pajak')
            ->get();

        // High-Priority Companies (Those with most overdue)
        $topDefaulters = Pajak::select('nama_perusahaan', 'logo_url', DB::raw('count(*) as overdue_count'))
            ->where('status', 'terlambat')
            ->groupBy('nama_perusahaan', 'logo_url')
            ->orderBy('overdue_count', 'desc')
            ->limit(10) // More in PDF for completeness
            ->get();

        $overallStats = [
            'total_records' => Pajak::count(),
            'avg_completion' => Pajak::where('status', 'selesai')->count() / max(1, Pajak::count()) * 100,
            'critical_records' => Pajak::where('status', 'terlambat')->count()
        ];

        $pdf = Pdf::loadView('laporan.pdf', compact('taxTypeStats', 'topDefaulters', 'overallStats'));
        
        return $pdf->download('TaxFlow_Audit_Report_' . now()->format('Y-m-d') . '.pdf');
    }
}
