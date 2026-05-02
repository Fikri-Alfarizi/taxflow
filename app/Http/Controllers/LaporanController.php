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

    public function export(Request $request)
    {
        $queryBase = Pajak::query();

        // Filter dari modal ekspor
        if ($request->filled('dari_tanggal')) {
            $queryBase->whereDate('tanggal_jatuh_tempo', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $queryBase->whereDate('tanggal_jatuh_tempo', '<=', $request->sampai_tanggal);
        }

        // Aggregate Completion Rates per Tax Type
        $taxTypeStats = clone $queryBase;
        $taxTypeStats = $taxTypeStats->select('jenis_pajak', 
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "selesai" then 1 else 0 end) as completed'),
                DB::raw('sum(case when status = "terlambat" then 1 else 0 end) as overdue')
            )
            ->groupBy('jenis_pajak')
            ->get();

        // High-Priority Companies (Those with most overdue)
        $topDefaultersQuery = clone $queryBase;
        $topDefaulters = $topDefaultersQuery->select('nama_perusahaan', 'logo_url', DB::raw('count(*) as overdue_count'))
            ->where('status', 'terlambat')
            ->groupBy('nama_perusahaan', 'logo_url')
            ->orderBy('overdue_count', 'desc')
            ->limit(10) // More in PDF/Excel for completeness
            ->get();

        $countQuery = clone $queryBase;
        $completedQuery = clone $queryBase;
        $lateQuery = clone $queryBase;

        $totalRecords = $countQuery->count();
        
        $overallStats = [
            'total_records' => $totalRecords,
            'avg_completion' => $completedQuery->where('status', 'selesai')->count() / max(1, $totalRecords) * 100,
            'critical_records' => $lateQuery->where('status', 'terlambat')->count()
        ];

        $tipe_ekspor = $request->get('tipe_ekspor', 'excel');
        $filename = 'TaxFlow_Audit_Report_' . now()->format('Y_m_d_His');

        if ($tipe_ekspor === 'excel') {
            return response(view('laporan.excel', compact('taxTypeStats', 'topDefaulters', 'overallStats')))
                ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');
        } else {
            $pdf = Pdf::loadView('laporan.pdf', compact('taxTypeStats', 'topDefaulters', 'overallStats'));
            return $pdf->download($filename . '.pdf');
        }
    }
}
