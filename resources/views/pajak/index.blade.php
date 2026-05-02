@extends('layouts.app') 

@section('title', 'Data Proses Pajak') 
@section('page-title', 'Overview Perpajakan') 

@section('actions') 
    <div class="flex items-center gap-2"> 
        <form action="{{ route('pajak.sync') }}" method="POST" class="inline"> 
            @csrf 
            <button type="submit" class="flex items-center gap-2 px-3 py-1.5 bg-slate-800 dark:bg-slate-700 text-white rounded-lg text-[10px] font-black hover:bg-black dark:hover:bg-slate-600 transition-all shadow-xl group"> 
                <i class="ph ph-arrows-clockwise text-base group-hover:rotate-180 transition-transform duration-500"></i> <span class="hidden md:inline">SINKRONISASI API</span> 
            </button> 
        </form> 
        <button type="button" onclick="openExportModal()" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-lg text-[10px] font-black hover:bg-slate-50 dark:hover:bg-slate-700 transition-all"> 
            <i class="ph ph-export text-base"></i> <span class="hidden md:inline">EKSPOR DATA</span> 
        </button> 
        <a href="{{ route('pajak.create') }}" class="flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[10px] font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 dark:shadow-none"> 
            <i class="ph ph-plus text-base"></i> <span class="hidden md:inline">TAMBAH DATA</span> 
        </a> 
    </div> 
@endsection 

@section('content') 
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-4 mb-6 transition-all"> 
    <form action="{{ route('pajak.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end"> 
        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}"> 
        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}"> 
        
        <div class="md:col-span-4"> 
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5">Pencarian Universal</label> 
            <div class="relative"> 
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-500"></i> 
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perusahaan..." 
                    class="w-full pl-9 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:bg-white dark:focus:bg-slate-900 transition-all"> 
            </div> 
        </div> 
        
        <div class="md:col-span-2 hidden md:block"> 
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5">Status Progres</label> 
            <select name="status" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all appearance-none"> 
                <option value="">SEMUA STATUS</option> 
                <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>PROSES</option> 
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>SELESAI</option> 
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>TERLAMBAT</option> 
            </select> 
        </div> 
        
        <div class="md:col-span-3 hidden md:block"> 
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5">Masa Jatuh Tempo</label> 
            <div class="flex items-center gap-1.5"> 
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="w-full px-2 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-bold text-slate-800 dark:text-slate-200 uppercase"> 
            </div> 
        </div> 
        
        <div class="md:col-span-3 flex gap-2"> 
            <button type="submit" class="flex-1 px-4 py-1.5 bg-slate-800 dark:bg-slate-700 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md">Filter</button> 
            <a href="{{ route('pajak.index') }}" wire:navigate class="px-4 py-1.5 bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-800 transition-all text-center flex items-center justify-center">Reset</a> 
        </div> 
    </form> 
</div> 

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all"> 
    <div class="overflow-x-auto scrollbar-hide"> 
        <table class="w-full text-[11px]"> 
            <thead> 
                <tr class="bg-slate-50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] border-b border-slate-100 dark:border-slate-700"> 
                    <th class="px-5 py-3 text-left"> 
                        <a href="{{ route('pajak.index', array_merge(request()->query(), ['sort_by' => 'nama_perusahaan', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"> 
                            Identitas Klien <i class="ph ph-caret-up-down opacity-30"></i> 
                        </a> 
                    </th> 
                    <th class="px-5 py-3 text-left hidden lg:table-cell"> 
                        <a href="{{ route('pajak.index', array_merge(request()->query(), ['sort_by' => 'jenis_pajak', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"> 
                            Klasifikasi Pajak <i class="ph ph-caret-up-down opacity-30"></i> 
                        </a> 
                    </th> 
                    <th class="px-5 py-3 text-left hidden md:table-cell"> 
                        <a href="{{ route('pajak.index', array_merge(request()->query(), ['sort_by' => 'tanggal_jatuh_tempo', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"> 
                            Jatuh Tempo <i class="ph ph-caret-up-down opacity-30"></i> 
                        </a> 
                    </th> 
                    <th class="px-5 py-3 text-center">Deadline</th> 
                    <th class="px-5 py-3 text-center">Monitoring</th> 
                    <th class="px-5 py-3 text-center">Perbaikan</th> 
                    <th class="px-5 py-3 text-right">Opsi</th> 
                </tr> 
            </thead> 
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-bold uppercase tracking-tight text-slate-600 dark:text-slate-400"> 
                @forelse($pajaks as $p) 
                @php $days = $p->sisa_hari; @endphp 
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all border-l-2 border-l-transparent hover:border-l-blue-600"> 
                    <td class="px-5 py-3"> 
                        <div class="flex items-center gap-3" x-data="{ logoError: false }"> 
                            <div class="flex w-7 h-7 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700 items-center justify-center overflow-hidden shrink-0 transition-all shadow-sm group-hover:border-blue-500/30"> 
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
                    <td class="px-5 py-3 text-slate-400 dark:text-slate-500 text-[10px] hidden lg:table-cell"> 
                        {{ $p->jenis_pajak }} <span class="text-slate-200 dark:text-slate-700">/</span> {{ $p->periode }} 
                    </td> 
                    <td class="px-5 py-3 whitespace-nowrap text-slate-500 dark:text-slate-400 hidden md:table-cell"> 
                        {{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/y') }} 
                    </td> 
                    <td class="px-5 py-3 text-center tabular-nums"> 
                        @if($p->status === 'selesai') 
                            <span class="text-emerald-500 text-[9px] font-black">STABIL</span> 
                        @elseif($days < 0) 
                            <span class="text-rose-600 dark:text-rose-400 font-black">{{ abs($days) }}H LALU</span> 
                        @elseif($days <= 3) 
                            <span class="text-amber-500 font-black animate-pulse">{{ $days == 0 ? 'NOW' : $days . 'H' }}</span> 
                        @else 
                            <span class="text-slate-300 dark:text-slate-600">{{ $days }}H</span> 
                        @endif 
                    </td> 
                    <td class="px-2 py-3 text-center"> 
                        @if($p->status == 'diproses') 
                            <div class="w-2 h-2 rounded-full bg-amber-500 mx-auto shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div> 
                        @elseif($p->status == 'selesai') 
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mx-auto"></div> 
                        @else 
                            <div class="w-2 h-2 rounded-full bg-rose-500 mx-auto animate-ping"></div> 
                        @endif 
                    </td> 
                    <td class="px-2 py-3 text-center"> 
                        @if($p->catatanPerbaikans->count() > 0) 
                            @php $catatan = $p->catatanPerbaikans->first(); @endphp 
                            @if($catatan->status === 'belum_diperbaiki') 
                                @if($p->user_id === auth()->id()) 
                                    <button type="button" 
                                            onclick="openModal(this)"
                                            data-catatan-id="{{ $catatan->id }}" 
                                            data-catatan-text="{{ $catatan->catatan_perbaikan }}" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-[9px] font-black rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 animate-pulse border border-rose-400"> 
                                        <i class="ph ph-wrench text-xs"></i> 
                                        <span>PERBAIKI</span> 
                                    </button> 
                                @else 
                                    <span class="text-slate-400 dark:text-slate-500 text-[10px] font-semibold">Tugas perbaikan untuk staff lain</span> 
                                @endif 
                            @elseif($catatan->status === 'sedang_diperbaiki') 
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 text-[8px] font-black rounded-lg border border-amber-200"> 
                                    <i class="ph ph-clock text-xs"></i> 
                                    <span>DIPERBAIKI</span> 
                                </span> 
                            @else 
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-800 text-[8px] font-black rounded-lg border border-emerald-200"> 
                                    <i class="ph ph-check-circle text-xs"></i> 
                                    <span>SELESAI</span> 
                                </span> 
                            @endif 
                        @else 
                            <span class="text-slate-300 dark:text-slate-600 text-[10px] font-medium">-</span> 
                        @endif 
                    </td> 
                    <td class="px-5 py-3 text-right"> 
                        <div class="flex items-center justify-end gap-1"> 
                            <a href="{{ route('pajak.show', $p->id) }}" wire:navigate class="p-1 text-slate-300 dark:text-slate-600 hover:text-blue-600 transition-all"><i class="ph ph-eye text-lg"></i></a> 
                            <a href="{{ route('pajak.edit', $p->id) }}" wire:navigate class="p-1 text-slate-300 dark:text-slate-600 hover:text-amber-500 transition-all"><i class="ph ph-pencil-simple text-lg"></i></a> 
                            @if(auth()->user()->isAdmin()) 
                            <button @click="confirmDelete('{{ route('pajak.destroy', $p->id) }}', '{{ $p->nama_perusahaan }}')" class="p-1 text-slate-300 dark:text-slate-600 hover:text-rose-500 transition-all"> 
                                <i class="ph ph-trash text-lg"></i> 
                            </button> 
                            @endif 
                        </div> 
                    </td> 
                </tr> 
                @empty 
                <tr> 
                    <td colspan="7" class="px-5 py-20 text-center text-slate-300 dark:text-slate-600 font-black italic text-[10px] tracking-widest uppercase">DATABASE_EMPTY_RESULT_FAILSAFE</td> 
                </tr> 
                @endforelse 
            </tbody> 
        </table> 
    </div> 
    
    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4"> 
        <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2"> 
            ROW: {{ $pajaks->firstItem() ?? 0 }}—{{ $pajaks->lastItem() ?? 0 }} 
            <span class="w-1 h-1 bg-slate-200 dark:bg-slate-700 rounded-full"></span> 
            TOTAL: {{ $pajaks->total() }} 
        </div> 
        @if($pajaks->hasPages()) 
        <nav class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0"> 
            <a href="{{ $pajaks->previousPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ $pajaks->onFirstPage() ? 'opacity-20 pointer-events-none' : '' }}"> 
                <i class="ph ph-caret-left font-black"></i> 
            </a> 
            @foreach ($pajaks->getUrlRange(max(1, $pajaks->currentPage() - 1), min($pajaks->lastPage(), $pajaks->currentPage() + 1)) as $page => $url) 
                <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all {{ $page == $pajaks->currentPage() ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}"> 
                    {{ $page }} 
                </a> 
            @endforeach 
            <a href="{{ $pajaks->nextPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ !$pajaks->hasMorePages() ? 'opacity-20 pointer-events-none' : '' }}"> 
                <i class="ph ph-caret-right font-black"></i> 
            </a> 
        </nav> 
        @endif 
    </div> 
</div> 

<div id="catatanModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0" aria-labelledby="catatanModalLabel" aria-modal="true" role="dialog"> 
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all scale-95 opacity-0" id="modalPanel"> 
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex justify-between items-center"> 
            <h5 class="font-bold flex items-center gap-2 text-base"> 
                <i class="ph ph-wrench text-xl"></i> 
                Catatan Perbaikan Data 
            </h5> 
            <button type="button" onclick="closeModal()" class="text-white/80 hover:text-white transition-colors"> 
                <i class="ph ph-x text-xl"></i> 
            </button> 
        </div> 
        <form id="catatanForm" method="POST" class="needs-validation" novalidate> 
            @csrf 
            <div class="p-6"> 
                <div class="mb-5"> 
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center"> 
                        <i class="ph ph-warning-circle text-amber-500 mr-1.5 text-lg"></i> 
                        Catatan dari Admin: 
                    </label> 
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4"> 
                        <p id="catatanText" class="text-slate-800 dark:text-slate-200 mb-0 font-medium leading-relaxed"></p> 
                    </div> 
                </div> 
                
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"> 
                    <div class="flex items-start gap-3"> 
                        <input type="checkbox" id="konfirmasiCheck" class="mt-1 w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2" required> 
                        <label for="konfirmasiCheck" class="text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer leading-relaxed"> 
                            <span class="block font-bold text-blue-800 dark:text-blue-300 mb-1">Konfirmasi Perbaikan</span> 
                            Saya telah memperbaiki data sesuai catatan di atas dan memastikan semua informasi sudah akurat. 
                        </label> 
                    </div> 
                </div> 
            </div> 
            <div class="border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4 flex justify-end gap-3"> 
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-colors flex items-center"> 
                    <i class="ph ph-x mr-1"></i> Batal 
                </button> 
                <button type="submit" id="selesaiBtn" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-lg font-bold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center" disabled> 
                    <i class="ph ph-check mr-1"></i> Selesai Perbaikan 
                </button> 
            </div> 
        </form> 
    </div> 
</div> 

<!-- Modal Export Data -->
<div id="exportModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0" aria-labelledby="exportModalLabel" aria-modal="true" role="dialog"> 
    <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transform transition-all scale-95 opacity-0" id="exportModalPanel"> 
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white p-4 flex justify-between items-center"> 
            <h5 class="font-bold flex items-center gap-2 text-base"> 
                <i class="ph ph-export text-xl"></i> 
                Parameter Ekspor Data 
            </h5> 
            <button type="button" onclick="closeExportModal()" class="text-white/80 hover:text-white transition-colors"> 
                <i class="ph ph-x text-xl"></i> 
            </button> 
        </div> 
        <form action="{{ route('pajak.export') }}" method="GET" class="needs-validation" novalidate id="formExportPayload"> 
            <div class="p-6"> 
                
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Mulai Tanggal</label>
                        <input type="date" name="dari_tanggal" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Status Progres</label>
                    <select name="status" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none cursor-pointer"> 
                        <option value="">Semua Status</option> 
                        <option value="diproses">Proses</option> 
                        <option value="selesai">Selesai</option> 
                        <option value="terlambat">Terlambat</option> 
                    </select>
                </div>
                
                <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/30 rounded-xl p-4"> 
                    <label class="block text-[10px] font-black text-indigo-800 dark:text-indigo-400 uppercase tracking-widest mb-3">Format Ekspor Final</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe_ekspor" value="excel" class="peer sr-only" checked>
                            <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-lg peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 hover:border-emerald-200 transition-all">
                                <i class="ph ph-microsoft-excel-logo text-2xl text-emerald-600 dark:text-emerald-400"></i>
                                <div>
                                    <h6 class="text-xs font-black text-slate-800 dark:text-slate-200">EXCEL</h6>
                                    <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500">.xls Spreadsheet</p>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe_ekspor" value="pdf" class="peer sr-only">
                            <div class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-lg peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20 hover:border-rose-200 transition-all">
                                <i class="ph ph-file-pdf text-2xl text-rose-600 dark:text-rose-400"></i>
                                <div>
                                    <h6 class="text-xs font-black text-slate-800 dark:text-slate-200">PDF</h6>
                                    <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500">Printable Doc</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div> 

            </div> 
            <div class="border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4 flex justify-end gap-3"> 
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-black transition-colors flex items-center"> 
                    BATAL 
                </button> 
                <button type="submit" onclick="setTimeout(closeExportModal, 1000)" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg text-xs font-black transition-all duration-200 shadow-lg shadow-indigo-200 dark:shadow-none flex items-center"> 
                    <i class="ph ph-download-simple mr-2 text-sm"></i> UNDUH SEKARANG 
                </button> 
            </div> 
        </form> 
    </div> 
</div> 

<script> 
    // Fungsi buka modal (Menggantikan bs-toggle)
    function openModal(button) {
        const modal = document.getElementById('catatanModal');
        const modalPanel = document.getElementById('modalPanel');
        const catatanId = button.getAttribute('data-catatan-id');
        const catatanText = button.getAttribute('data-catatan-text');
        
        // Isi text dan action ke dalam form
        document.getElementById('catatanText').textContent = catatanText;
        document.getElementById('catatanForm').action = `/catatan-perbaikan/${catatanId}/selesai`;
        
        // Reset validasi
        document.getElementById('konfirmasiCheck').checked = false;
        document.getElementById('selesaiBtn').disabled = true;

        // Tampilkan dengan animasi Tailwind
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Timeout pendek agar class flex ter-render dulu sebelum transisi opacity & scale
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalPanel.classList.remove('scale-95', 'opacity-0');
            modalPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Fungsi tutup modal catatan
    function closeModal() {
        const modal = document.getElementById('catatanModal');
        const modalPanel = document.getElementById('modalPanel');

        modal.classList.add('opacity-0');
        modalPanel.classList.remove('scale-100', 'opacity-100');
        modalPanel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }

    // Modal Export functions
    function openExportModal() {
        const modal = document.getElementById('exportModal');
        const modalPanel = document.getElementById('exportModalPanel');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalPanel.classList.remove('scale-95', 'opacity-0');
            modalPanel.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        const modalPanel = document.getElementById('exportModalPanel');

        modal.classList.add('opacity-0');
        modalPanel.classList.remove('scale-100', 'opacity-100');
        modalPanel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function() { 
        // Handle checkbox change
        const checkValidasi = document.getElementById('konfirmasiCheck');
        if(checkValidasi) {
            checkValidasi.addEventListener('change', function() { 
                document.getElementById('selesaiBtn').disabled = !this.checked; 
            }); 
        }

        // Handle form submission dengan loading state
        const formSubmit = document.getElementById('catatanForm');
        if(formSubmit) {
            formSubmit.addEventListener('submit', function(e) { 
                const submitBtn = document.getElementById('selesaiBtn'); 
                const originalText = submitBtn.innerHTML; 
                
                // Show loading state 
                submitBtn.disabled = true; 
                submitBtn.innerHTML = '<i class="ph ph-spinner ph-spin mr-1"></i> Menyimpan...'; 
                
                // Re-enable in case of submission block 
                setTimeout(() => { 
                    submitBtn.disabled = false; 
                    submitBtn.innerHTML = originalText; 
                }, 3000); 
            }); 
        }
    }); 
</script> 
@endsection