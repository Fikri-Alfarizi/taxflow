@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Pemantauan Aktivitas')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
<div x-data="dashboardStats({
    initialStats: {{ json_encode($stats) }},
    apiUrl: '{{ route('api.dashboard.stats') }}'
})">
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 lg:gap-3 mb-4">
    <template x-for="(card, key) in cards" :key="key">
        <div class="bg-white dark:bg-slate-800 p-3 rounded-[1.5rem] border border-slate-200/60 dark:border-slate-700/60 flex flex-col justify-between transition-all hover:bg-slate-50 dark:hover:bg-slate-800/80">
            <div class="flex items-center justify-between mb-1.5">
                <div class="w-7 h-7 rounded-[10px] flex items-center justify-center" :class="card.color">
                    <i class="ph text-lg" :class="card.icon"></i>
                </div>
                <span class="text-[7px] font-bold text-emerald-500 uppercase flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> LIVE
                </span>
            </div>
            <div>
                <h3 class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1" x-text="card.label"></h3>
                <p class="text-base font-black text-slate-800 dark:text-white tabular-nums leading-none" x-text="formatNumber(card.value)"></p>
            </div>
        </div>
    </template>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
    <div class="lg:col-span-8 space-y-3 lg:space-y-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/60">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-[9px] font-bold text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-1.5">
                    <i class="ph ph-chart-line text-blue-600 text-base"></i> Tren Ingesti
                </h3>
            </div>
            <div class="h-36 w-full overflow-hidden">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/60 overflow-hidden min-h-[250px]">
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-[9px] font-bold text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-1.5">
                    <i class="ph ph-warning-circle text-rose-500 text-base"></i> Prioritas Klien
                </h3>
            </div>
            <div class="overflow-x-auto scrollbar-hide">
                <table class="w-full text-[9px]">
                    <thead>
                        <tr class="bg-transparent text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700/50">
                            <th class="text-left px-4 py-2.5">Identitas</th>
                            <th class="text-left px-4 py-2.5 hidden md:table-cell">Masa</th>
                            <th class="text-left px-4 py-2.5 hidden sm:table-cell">Batas</th>
                            <th class="text-center px-4 py-2.5">Status</th>
                            <th class="text-right px-4 py-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30 font-semibold uppercase tracking-tight text-slate-600 dark:text-slate-400">
                        @forelse($warningPajak as $p)
                        @php $days = $p->sisa_hari; @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors {{ $days < 0 ? 'bg-rose-50/20 dark:bg-rose-500/5' : '' }}">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2" x-data="{ logoError: false }">
                                    <div class="flex w-6 h-6 rounded-[8px] bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 items-center justify-center overflow-hidden shrink-0">
                                        @if($p->logo_url)
                                            <img src="{{ $p->logo_url }}" alt="{{ $p->nama_perusahaan }}" class="w-full h-full object-contain p-0.5" x-show="!logoError" x-on:error="logoError = true">
                                            <span x-show="logoError" x-cloak class="text-[8px] font-bold text-slate-400">{{ substr($p->nama_perusahaan, 0, 1) }}</span>
                                        @else
                                            <span class="text-[8px] font-bold text-slate-400">{{ substr($p->nama_perusahaan, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-slate-800 dark:text-slate-200 truncate max-w-[120px] md:max-w-none">{{ $p->nama_perusahaan }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-slate-500 dark:text-slate-500 hidden md:table-cell">{{ $p->jenis_pajak }}</td>
                            <td class="px-4 py-2 whitespace-nowrap hidden sm:table-cell tabular-nums">
                                <span class="{{ $days < 0 ? 'text-rose-500 font-bold' : 'text-amber-500 font-bold' }}">{{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/y') }}</span>
                            </td>
                            <td class="px-4 py-2 text-center text-[8px]">
                                @if($days < 0)
                                    <span class="text-rose-600 dark:text-rose-400 font-bold">TERLAMBAT {{ abs($days) }}H</span>
                                @elseif($days == 0)
                                    <span class="text-amber-600 dark:text-amber-400 font-bold">HARI INI</span>
                                @else
                                    <span class="text-slate-500 dark:text-slate-400 font-semibold">{{ $days }}H LAGI</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('pajak.show', $p->id) }}" wire:navigate class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[8px] font-bold tracking-widest bg-blue-50 dark:bg-blue-500/10 px-2 py-1 rounded-[8px]">VIEW</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400 font-bold text-[9px] tracking-widest uppercase">ALL_SYSTEMS_STABLE</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="lg:col-span-4 space-y-3 lg:space-y-4">
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/60 overflow-hidden h-fit">
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <h3 class="text-[9px] font-bold text-slate-800 dark:text-white uppercase tracking-widest leading-none">Koneksi</h3>
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-2 border-l-2 border-slate-100 dark:border-slate-700/50 pl-3 ml-1">
                    <div class="flex flex-col text-[8px] font-bold uppercase mb-2">
                        <span class="text-slate-400 dark:text-slate-500 tracking-widest mb-0.5">Sinkronisasi</span>
                        <span class="text-slate-800 dark:text-slate-200 tabular-nums text-[9px]" x-text="apiStatus.updated_at"></span>
                    </div>
                    <div class="flex flex-col text-[8px] font-bold uppercase">
                        <span class="text-slate-400 dark:text-slate-500 tracking-widest mb-0.5">Log Terakhir</span>
                        <span class="text-slate-800 dark:text-slate-200 truncate text-[9px]" x-text="apiStatus.last_sync"></span>
                    </div>
                </div>
                <form action="{{ route('pajak.sync') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-[1rem] text-[9px] font-bold uppercase tracking-widest transition-all">
                        Fetch Data
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/60">
            <h3 class="text-[9px] font-bold text-slate-800 dark:text-white mb-3 uppercase tracking-widest">Komposisi</h3>
            <div class="h-24 flex justify-center items-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200/60 dark:border-slate-700/60 overflow-hidden h-fit">
            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-[9px] font-bold text-slate-800 dark:text-white uppercase tracking-widest">Aktivitas</h3>
            </div>
            <div class="p-4 space-y-3 max-h-[200px] overflow-y-auto scrollbar-hide">
                @forelse($activities as $act)
                <div class="flex gap-2.5">
                    <div class="shrink-0 w-5 h-5 rounded-[8px] bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <i class="ph text-sm {{ $act->status_proses == 'selesai' ? 'ph-check text-emerald-500' : ($act->status_proses == 'terlambat' ? 'ph-warning text-rose-500' : 'ph-arrows-clockwise text-blue-500') }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[9px] text-slate-800 dark:text-slate-200 font-bold truncate uppercase leading-none mb-0.5">{{ optional($act->pajak)->nama_perusahaan ?? 'SYSTEM' }}</p>
                        <p class="text-[8px] text-slate-400 font-semibold uppercase tracking-widest">{{ \Carbon\Carbon::parse($act->tanggal_update)->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-slate-400 text-[8px] font-bold uppercase tracking-widest">NO_LOGS</div>
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
                {label: 'Total Data', value: this.stats.total, icon: 'ph-files', color: 'bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400'},
                {label: 'Proses', value: this.stats.diproses, icon: 'ph-clock', color: 'bg-amber-50 dark:bg-amber-500/10 text-amber-500'},
                {label: 'Selesai', value: this.stats.selesai, icon: 'ph-check-circle', color: 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500'},
                {label: 'Terlambat', value: this.stats.terlambat, icon: 'ph-warning', color: 'bg-rose-50 dark:bg-rose-500/10 text-rose-500'},
                {label: 'Mendekati', value: this.stats.mendekati, icon: 'ph-alarm', color: 'bg-orange-50 dark:bg-orange-500/10 text-orange-500'},
                {label: 'Dokumen', value: this.stats.dokumen, icon: 'ph-folder-open', color: 'bg-blue-50 dark:bg-blue-500/10 text-blue-500'},
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
            const textColor = isDark ? '#64748b' : '#94a3b8';

            if (window.trendChartInstance) {
                window.trendChartInstance.options.scales.x.grid.color = gridColor;
                window.trendChartInstance.options.scales.x.ticks.color = textColor;
                window.trendChartInstance.update();
            }
        }
    }));
});

document.addEventListener('livewire:navigation:complete', () => { initCharts(); });
document.addEventListener('livewire:navigated', () => { initCharts(); });

function initCharts() {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.05)' : 'rgba(148, 163, 184, 0.1)';
    const textColor = isDark ? '#64748b' : '#94a3b8';

    const trendCtx = document.getElementById('trendChart')?.getContext('2d');
    if(trendCtx && !window.trendChartInstance) {
        window.trendChartInstance = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trend['labels']) !!},
                datasets: [{
                    data: {!! json_encode($trend['data']) !!},
                    borderColor: '#2563eb',
                    backgroundColor: isDark ? 'rgba(37, 99, 235, 0.1)' : 'rgba(37, 99, 235, 0.05)',
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
                        ticks: { font: { size: 8, weight: '700' }, color: textColor, padding: 8 } 
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
            options: { cutout: '80%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
}
</script>
@endsection