@extends('layouts.app')

@section('title', 'Monitoring Pajak')
@section('page-title', 'Log Aktivitas & Monitoring')

@section('content')
<div x-data="{ 
    monitorings: [],
    latestId: {{ $monitorings->first() ? $monitorings->first()->id : 0 }},
    isPaused: false,
    async fetchUpdates() {
        if (this.isPaused) return;
        try {
            const response = await fetch(`{{ route('api.monitoring.updates') }}?after_id=${this.latestId}`);
            const data = await response.json();
            if (data.updates && data.updates.length > 0) {
                this.monitorings = [...data.updates, ...this.monitorings];
                this.latestId = data.latest_id;
            }
        } catch (error) {
            console.error('Telemetri Error:', error);
        }
    }
}" x-init="setInterval(() => fetchUpdates(), 5000)" class="space-y-6">
<!-- Filter Section -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-4 lg:p-6 mb-6 mt-2 transition-all duration-300">
    <form action="{{ route('monitoring.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-5">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5 px-1">Pencarian Entitas</label>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama perusahaan..."
                    class="w-full pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all">
            </div>
        </div>

        <div class="md:col-span-3 hidden md:block">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5 px-1">Parameter Status</label>
            <select name="status_proses" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-xl text-[11px] font-black text-slate-800 dark:text-slate-200 focus:outline-none transition-all appearance-none">
                <option value="">SEMUA_STATUS</option>
                <option value="diproses" {{ request('status_proses') == 'diproses' ? 'selected' : '' }}>PROSES</option>
                <option value="selesai" {{ request('status_proses') == 'selesai' ? 'selected' : '' }}>SELESAI</option>
                <option value="terlambat" {{ request('status_proses') == 'terlambat' ? 'selected' : '' }}>TERLAMBAT</option>
            </select>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="flex-1 px-4 py-1.5 bg-slate-800 dark:bg-slate-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all">Filter</button>
            <a href="{{ route('monitoring.index') }}" class="px-4 py-1.5 bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Log Timeline / Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <th class="px-6 py-4 text-left">Audit ID / Telemetri</th>
                    <th class="px-6 py-4 text-left">Subjek Operasional</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-left hidden lg:table-cell">Narasi Progres</th>
                    <th class="px-6 py-4 text-left hidden md:table-cell">Laporan Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 uppercase text-[9px] tracking-wide font-black transition-all duration-300">
                <!-- Real-Time Appended Updates -->
                <template x-for="log in monitorings" :key="log.id">
                    <tr class="bg-blue-50/20 dark:bg-blue-900/10 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-all border-l-2 border-l-blue-600 animate-pulse-once">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-blue-600 dark:text-blue-400 font-black" x-text="log.audit_id"></span>
                                <span class="text-[8px] text-slate-400 dark:text-slate-500 mt-1 tabular-nums font-bold" x-text="log.timestamp"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <a :href="log.pajak_url" class="text-blue-600 dark:text-blue-400 font-black hover:underline mb-0.5 truncate max-w-[120px] md:max-w-xs" x-text="log.perusahaan"></a>
                                <span class="text-slate-400 dark:text-slate-500 text-[8px] font-bold">
                                    <span x-text="log.jenis_pajak"></span> 
                                    <span class="text-slate-200 dark:text-slate-800 mx-1">|</span> 
                                    <span x-text="log.periode"></span>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-0.5 rounded font-black border transition-all text-[8px]" :class="log.status_class" x-text="log.status_label"></span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <p class="text-slate-500 dark:text-slate-400 normal-case italic max-w-xs leading-relaxed truncate" x-text="log.catatan"></p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 flex items-center justify-center font-black text-[8px] border border-slate-200 dark:border-slate-700 shadow-sm" x-text="log.user_initial"></div>
                                <span class="text-slate-400 dark:text-slate-600 lowercase font-bold tracking-tighter" x-text="log.user_name"></span>
                            </div>
                        </td>
                    </tr>
                </template>

                @forelse($monitorings as $log)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all border-l-2 border-l-transparent hover:border-l-blue-600">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-slate-800 dark:text-slate-200 font-black">#LOG-{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[8px] text-slate-300 dark:text-slate-600 mt-1 tabular-nums font-bold">{{ \Carbon\Carbon::parse($log->tanggal_update)->format('d/m/y H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->pajak)
                        <div class="flex flex-col">
                            <a href="{{ route('pajak.show', $log->pajak_id) }}" class="text-blue-600 dark:text-blue-400 font-black hover:underline mb-0.5 truncate max-w-[120px] md:max-w-xs">{{ $log->pajak->nama_perusahaan }}</a>
                            <span class="text-slate-400 dark:text-slate-500 text-[8px] font-bold">{{ $log->pajak->jenis_pajak }} <span class="text-slate-200 dark:text-slate-800 mx-1">|</span> {{ $log->pajak->periode }}</span>
                        </div>
                        @else
                        <span class="text-rose-300 dark:text-rose-950 italic font-black text-[8px] uppercase">DATA_PURGED</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($log->status_proses == 'diproses')
                            <span class="px-2 py-0.5 rounded font-black bg-amber-50 dark:bg-amber-950/30 text-amber-500 border border-amber-100 dark:border-amber-900 transition-all text-[8px]">PROSES</span>
                        @elseif($log->status_proses == 'selesai')
                            <span class="px-2 py-0.5 rounded font-black bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 border border-emerald-100 dark:border-emerald-900 transition-all text-[8px]">STABIL</span>
                        @else
                            <span class="px-2 py-0.5 rounded font-black bg-rose-50 dark:bg-rose-950/30 text-rose-500 border border-rose-100 dark:border-rose-900 transition-all text-[8px]">KRITIS</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <p class="text-slate-500 dark:text-slate-400 normal-case italic max-w-xs leading-relaxed truncate" title="{{ $log->catatan }}">
                            {{ $log->catatan ?: '-' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-600 flex items-center justify-center font-black text-[8px] border border-slate-100 dark:border-slate-800 shadow-sm">
                                {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                            </div>
                            <span class="text-slate-400 dark:text-slate-600 lowercase font-bold tracking-tighter">{{ $log->user->name ?? 'System' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-24 text-center grayscale opacity-50">
                        <div class="flex flex-col items-center gap-3">
                            <i class="ph ph-chart-line-up text-5xl text-slate-200 dark:text-slate-700"></i>
                            <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest italic">MONITORING_LOG_EMPTY_FAILSAFE</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- High-Performance Professional Pagination Footer -->
    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2">
            ROW: {{ $monitorings->firstItem() ?? 0 }}—{{ $monitorings->lastItem() ?? 0 }} 
            <span class="w-1 h-1 bg-slate-200 dark:bg-slate-700 rounded-full"></span>
            TOTAL: {{ $monitorings->total() }}
        </div>
        
        @if($monitorings->hasPages())
        <nav class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ $monitorings->previousPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ $monitorings->onFirstPage() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-left font-black"></i>
            </a>
            @foreach ($monitorings->getUrlRange(max(1, $monitorings->currentPage() - 1), min($monitorings->lastPage(), $monitorings->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all {{ $page == $monitorings->currentPage() ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $monitorings->nextPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ !$monitorings->hasMorePages() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-right font-black"></i>
            </a>
        </nav>
        @endif
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    @keyframes pulse-once {
        0% { background-color: rgba(37, 99, 235, 0.1); border-left-width: 4px; }
        100% { background-color: transparent; border-left-width: 2px; }
    }
    .animate-pulse-once {
        animation: pulse-once 2s ease-out forwards;
    }
</style>
@endpush
@push('scripts')
<script>
    // System-wide telemetri notification could be added here
</script>
@endpush
