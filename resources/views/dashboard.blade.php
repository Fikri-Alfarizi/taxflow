@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pemantauan Aktivitas Utama')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
<div x-data="dashboardStats({
    initialStats: {{ json_encode($stats) }},
    apiUrl: '{{ route('api.dashboard.stats') }}'
})">
<!-- Low-Profile Statistik Cards (6 Cards) -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4 mb-6">
    <template x-for="(card, key) in cards" :key="key">
        <div class="bg-white dark:bg-slate-800 p-3 lg:p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col justify-between transition-all hover:bg-slate-50/50 dark:hover:bg-slate-700/50">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" :class="card.color">
                    <i class="ph text-xl" :class="card.icon"></i>
                </div>
                <span class="text-[7px] lg:text-[8px] font-black text-emerald-500 uppercase flex items-center gap-1">
                    <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></span> LIVE
                </span>
            </div>
            <div>
                <h3 class="text-[8px] lg:text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest leading-none mb-1" x-text="card.label"></h3>
                <p class="text-lg lg:text-xl font-black text-slate-800 dark:text-white tabular-nums leading-none" x-text="formatNumber(card.value)"></p>
            </div>
        </div>
    </template>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
    <!-- Main Analytical Section -->
    <div class="lg:col-span-8 space-y-4 lg:space-y-6">
        <!-- Compact Trend Chart -->
        <div class="bg-white dark:bg-slate-800 p-4 lg:p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="ph ph-chart-line text-blue-600 text-lg"></i> Tren Ingesti Data
                </h3>
            </div>
            <div class="h-40 lg:h-48 w-full overflow-hidden">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- High-Density Priority Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden min-h-[300px]">
            <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/20 dark:bg-slate-800/10">
                <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="ph ph-warning-circle text-rose-500 text-lg"></i> Monitoring Prioritas
                </h3>
            </div>
            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full text-[10px]">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-700">
                            <th class="text-left px-5 py-3">Identitas Klien</th>
                            <th class="text-left px-5 py-3 hidden md:table-cell">Masa Pajak</th>
                            <th class="text-left px-5 py-3 hidden sm:table-cell">Deadline</th>
                            <th class="text-center px-5 py-3">Status</th>
                            <th class="text-right px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-bold uppercase tracking-tight text-slate-600 dark:text-slate-400">
                        @forelse($warningPajak as $p)
                        @php $days = $p->sisa_hari; @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors {{ $days < 0 ? 'bg-rose-50/5 dark:bg-rose-950/5' : '' }}">
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-3" x-data="{ logoError: false }">
                                    <div class="flex w-7 h-7 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700 items-center justify-center overflow-hidden shrink-0 transition-all shadow-sm">
                                        @if($p->logo_url)
                                            <img src="{{ $p->logo_url }}" alt="{{ $p->nama_perusahaan }}" class="w-full h-full object-contain p-1" x-show="!logoError" x-on:error="logoError = true">
                                            <span x-show="logoError" x-cloak class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase">{{ substr($p->nama_perusahaan, 0, 1) }}</span>
                                        @else
                                            <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase">{{ substr($p->nama_perusahaan, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-slate-800 dark:text-slate-100 truncate max-w-[150px] md:max-w-none">{{ $p->nama_perusahaan }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-slate-400 dark:text-slate-500 hidden md:table-cell">{{ $p->jenis_pajak }}</td>
                            <td class="px-5 py-2.5 whitespace-nowrap hidden sm:table-cell tabular-nums">
                                <span class="{{ $days < 0 ? 'text-rose-500' : 'text-amber-500' }}">{{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/y') }}</span>
                            </td>
                            <td class="px-5 py-2.5 text-center text-[9px]">
                                @if($days < 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-black tracking-tighter">TERLAMBAT {{ abs($days) }}H</span>
                                @elseif($days == 0)
                                    <span class="text-amber-600 dark:text-amber-400 font-black tracking-tighter">DEADLINE HARI INI</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 font-bold tracking-tighter uppercase">{{ $days }}H LAGI</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('pajak.show', $p->id) }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:underline text-[9px] font-black tracking-widest">VIEW →</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-slate-300 dark:text-slate-600 font-black italic text-[10px] tracking-widest uppercase">ALL_SYSTEMS_STABLE</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Side Utility (Compact) -->
    <div class="lg:col-span-4 space-y-4 lg:space-y-6">
        <!-- Connectivity Status -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden h-fit transition-all duration-500">
            <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-blue-50/5 dark:bg-slate-900/20 flex items-center justify-between">
                <h3 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Status Koneksi</h3>
                <div class="flex gap-1">
                    <span class="w-1 h-1 bg-emerald-500 rounded-full animate-ping"></span>
                    <span class="w-1 h-1 bg-emerald-500 rounded-full"></span>
                </div>
            </div>
            <div class="p-5">
                <div class="space-y-2.5 border-l border-slate-100 dark:border-slate-800 pl-4 ml-1">
                    <div class="flex flex-col text-[8px] font-black uppercase mb-3">
                        <span class="text-slate-300 dark:text-slate-600 tracking-[0.2em] mb-0.5">Sinkronisasi</span>
                        <span class="text-slate-800 dark:text-slate-300 tabular-nums text-[10px]" x-text="apiStatus.updated_at"></span>
                    </div>
                    <div class="flex flex-col text-[8px] font-black uppercase">
                        <span class="text-slate-300 dark:text-slate-600 tracking-[0.2em] mb-0.5">Log Terakhir</span>
                        <span class="text-slate-800 dark:text-slate-300 truncate text-[10px]" x-text="apiStatus.last_sync"></span>
                    </div>
                </div>
                <form action="{{ route('pajak.sync') }}" method="POST" class="mt-5">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-slate-800 dark:bg-slate-700 text-white rounded-lg text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black transition-all shadow-lg shadow-slate-100 dark:shadow-none">
                        FETCH API SEKARANG
                    </button>
                </form>
            </div>
        </div>

        <!-- Mini Distribution Chart -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all duration-500">
            <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white mb-4 uppercase tracking-widest">Komposisi Data</h3>
            <div class="h-28 flex justify-center items-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Compact Timeline Log -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm h-fit overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/10 dark:bg-slate-900/20">
                <h3 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest">Aktivitas Terkini</h3>
            </div>
            <div class="p-5 space-y-4 max-h-[250px] overflow-y-auto scrollbar-hide">
                @forelse($activities as $act)
                <div class="flex gap-3 group transition-all">
                    <div class="shrink-0 w-6 h-6 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center transition-colors group-hover:border-blue-500/30">
                        <i class="ph text-base {{ $act->status_proses == 'selesai' ? 'ph-check text-emerald-500' : ($act->status_proses == 'terlambat' ? 'ph-warning text-rose-500' : 'ph-arrows-clockwise text-blue-500') }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[9px] text-slate-800 dark:text-slate-100 font-black truncate tracking-tighter uppercase leading-none mb-1">{{ optional($act->pajak)->nama_perusahaan ?? 'SYSTEM' }}</p>
                        <p class="text-[8px] text-slate-300 dark:text-slate-600 font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($act->tanggal_update)->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-slate-200 dark:text-slate-700 text-[8px] font-black uppercase tracking-widest italic">TRACE_EMPTY</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardStats', ({initialStats, apiUrl}) => ({
        stats: initialStats,
        apiStatus: {
            last_sync: initialStats.last_sync || '{{ $lastSync }}',
            updated_at: '{{ now()->format('H:i:s') }}'
        },
        cards: [],

        init() {
            this.updateCards();
            setInterval(() => this.fetchStats(), 30000);
            window.addEventListener('theme-changed', (e) => this.recolorCharts(e.detail.isDark));
        },

        updateCards() {
            this.cards = [
                {label: 'Total Data', value: this.stats.total, icon: 'ph-files', color: 'bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-600'},
                {label: 'Dalam Proses', value: this.stats.diproses, icon: 'ph-clock', color: 'bg-amber-50 dark:bg-amber-950/30 text-amber-500'},
                {label: 'Selesai Lapor', value: this.stats.selesai, icon: 'ph-check-circle', color: 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500'},
                {label: 'Terlambat', value: this.stats.terlambat, icon: 'ph-warning', color: 'bg-rose-50 dark:bg-rose-950/30 text-rose-500'},
                {label: 'H-3 Deadline', value: this.stats.mendekati, icon: 'ph-alarm', color: 'bg-orange-50 dark:bg-orange-950/30 text-orange-500'},
                {label: 'Aset Dokumen', value: this.stats.dokumen, icon: 'ph-folder-open', color: 'bg-blue-50 dark:bg-blue-950/30 text-blue-500'},
            ];
            this.updateStatusChart();
        },

        async fetchStats() {
            try {
                const response = await fetch(apiUrl);
                const data = await response.json();
                this.stats = data;
                this.apiStatus.last_sync = data.last_sync;
                this.apiStatus.updated_at = data.updated_at;
                this.updateCards();
            } catch (error) {}
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        updateStatusChart() {
            if (window.statusChartInstance) {
                window.statusChartInstance.data.datasets[0].data = [this.stats.diproses, this.stats.selesai, this.stats.terlambat];
                window.statusChartInstance.update();
            }
        },

        recolorCharts(isDark) {
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.05)' : 'rgba(148, 163, 184, 0.1)';
            const textColor = isDark ? '#475569' : '#94a3b8';

            if (window.trendChartInstance) {
                window.trendChartInstance.options.scales.x.grid.color = gridColor;
                window.trendChartInstance.options.scales.x.ticks.color = textColor;
                window.trendChartInstance.update();
            }
        }
    }));
});

document.addEventListener('livewire:navigation:complete', () => { initCharts(); }); // Fallback
document.addEventListener('livewire:navigated', () => { initCharts(); });

function initCharts() {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.05)' : 'rgba(148, 163, 184, 0.1)';
    const textColor = isDark ? '#475569' : '#94a3b8';

    const trendCtx = document.getElementById('trendChart')?.getContext('2d');
    if(trendCtx && !window.trendChartInstance) {
        window.trendChartInstance = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trend['labels']) !!},
                datasets: [{
                    data: {!! json_encode($trend['data']) !!},
                    borderColor: '#2563eb',
                    backgroundColor: isDark ? 'rgba(37, 99, 235, 0.1)' : 'rgba(37, 99, 235, 0.03)',
                    fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, hoverRadius: 4
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false, 
                plugins: { legend: { display: false }, tooltip: { enabled: true } },
                scales: { 
                    y: { display: false }, 
                    x: { 
                        grid: { color: gridColor, drawTicks: false }, 
                        border: { display: false },
                        ticks: { font: { size: 7, weight: '900' }, color: textColor, padding: 10 } 
                    } 
                }
            }
        });
    }

    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if(statusCtx && !window.statusChartInstance) {
        window.statusChartInstance = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Proses', 'Selesai', 'Terlambat'],
                datasets: [{
                    data: [{{ $stats['diproses'] }}, {{ $stats['selesai'] }}, {{ $stats['terlambat'] }}],
                    backgroundColor: ['#f59e0b', '#10b981', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { cutout: '82%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
}
</script>
@endsection
