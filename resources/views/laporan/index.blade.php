@extends('layouts.app')

@section('title', 'Laporan Analitik')
@section('page-title', 'Ringkasan Analitis Manajemen')

@section('content')
<!-- High-Density Analytical Cards -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all">
        <div>
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest mb-1">Total Volume Audit</p>
            <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ number_format($overallStats['total_records']) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <i class="ph ph-database text-2xl"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between transition-all">
        <div>
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest mb-1">Efisiensi Penyelesaian</p>
            <p class="text-2xl font-black text-slate-800 dark:text-white tabular-nums">{{ number_format($overallStats['avg_completion'], 1) }}%</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
            <i class="ph ph-chart-line-up text-2xl"></i>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between border-l-4 border-l-rose-500 transition-all">
        <div>
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest mb-1">Defisit Kritis (Terlambat)</p>
            <p class="text-2xl font-black text-rose-600 dark:text-rose-400 tabular-nums">{{ number_format($overallStats['critical_records']) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
            <i class="ph ph-warning-circle text-2xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Detailed Performance by Tax Type (Table Dense) -->
    <div class="lg:col-span-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden h-full transition-all">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/20 dark:bg-slate-900/50">
                <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest">Rincian Efisiensi Alur Kerja Pajak</h3>
                <a href="{{ route('laporan.export.pdf') }}" target="_blank" class="text-[9px] font-black text-blue-600 dark:text-blue-400 hover:underline uppercase tracking-tight">Ekspor PDF <i class="ph ph-download"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-[11px]">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <th class="px-6 py-3 text-left">Klasifikasi</th>
                            <th class="px-6 py-3 text-center">Volume</th>
                            <th class="px-6 py-3 text-center">Selesai</th>
                            <th class="px-6 py-3 text-center">Lambat</th>
                            <th class="px-6 py-3 text-right">Hasil %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-bold uppercase tracking-tight text-slate-600 dark:text-slate-400">
                        @foreach($taxTypeStats as $stat)
                        <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-all">
                            <td class="px-6 py-3.5 text-slate-800 dark:text-slate-200">{{ $stat->jenis_pajak }}</td>
                            <td class="px-6 py-3.5 text-center px-4">{{ number_format($stat->total) }}</td>
                            <td class="px-6 py-3.5 text-center text-emerald-500 dark:text-emerald-400">{{ number_format($stat->completed) }}</td>
                            <td class="px-6 py-3.5 text-center text-rose-500 dark:text-rose-400">{{ number_format($stat->overdue) }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2 px-3">
                                    <span class="text-[9px] font-black leading-none">{{ number_format($stat->completed / max(1, $stat->total) * 100, 1) }}%</span>
                                    <div class="w-12 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 dark:bg-blue-500" style="width: {{ $stat->completed / max(1, $stat->total) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side Utility Analysis -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Monthly Volume Distribution -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all">
            <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest mb-4">Riwayat Beban Bulanan</h3>
            <div class="space-y-4">
                @foreach($monthlyTrend as $trend)
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-[10px] uppercase font-black tracking-tight">
                        <span class="text-slate-400 dark:text-slate-500">{{ $trend->month }}</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ number_format($trend->total) }} AUDIT</span>
                    </div>
                    <div class="w-full h-1 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-300 dark:bg-slate-600 transition-all duration-1000" style="width: {{ $trend->total / max(1, $monthlyTrend->max('total')) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Risk Profile (Top Overdue Companies) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
            <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 bg-rose-50/5 dark:bg-rose-950/10">
                <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest">Risiko Operasional (Terlambat Terbanyak)</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($topDefaulters as $risk)
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-700 flex items-center justify-center overflow-hidden shrink-0 transition-colors">
                        @if($risk->logo_url)
                            <img src="{{ $risk->logo_url }}" class="w-full h-full object-contain p-0.5">
                        @else
                            <span class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase leading-none">{{ substr($risk->nama_perusahaan, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-black text-slate-800 dark:text-slate-200 truncate uppercase leading-none mb-1">{{ $risk->nama_perusahaan }}</p>
                        <p class="text-[9px] font-bold text-rose-500 dark:text-rose-400 uppercase leading-none tracking-tight">{{ $risk->overdue_count }} Dokumen Terlambat Kritis</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
