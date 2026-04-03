@use('Illuminate\Support\Facades\Storage')
@extends('layouts.app')

@section('title', 'Dokumen Pajak')
@section('page-title', 'Pusat Dokumen Digital')

@section('content')
<!-- Filter Section -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 lg:p-6 mb-6 mt-2 transition-all">
    <form action="{{ route('dokumen.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-8">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest mb-2 px-1">Cari Aset Digital</label>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama file, perusahaan, atau jenis pajak..."
                    class="w-full pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all">
            </div>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="flex-1 px-4 py-1.5 bg-slate-800 dark:bg-slate-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md">Cari</button>
            <a href="{{ route('dokumen.index') }}" class="px-4 py-1.5 bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Documents List -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 transition-colors">
                    <th class="px-6 py-4 text-left">Label Digital / Tipe</th>
                    <th class="px-6 py-4 text-left hidden lg:table-cell">Data Pajak Terkait</th>
                    <th class="px-6 py-4 text-center hidden md:table-cell">Ukuran</th>
                    <th class="px-6 py-4 text-left hidden lg:table-cell">Upload</th>
                    <th class="px-6 py-4 text-right">Opsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 uppercase text-[9px] tracking-wide font-black text-slate-600 dark:text-slate-400 transition-all">
                @forelse($dokumens as $doc)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all border-l-2 border-l-transparent hover:border-l-blue-600">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center transition-colors shadow-sm">
                                @php $ext = pathinfo($doc->file_dokumen, PATHINFO_EXTENSION); @endphp
                                @if(in_array($ext, ['pdf']))
                                    <i class="ph ph-file-pdf text-xl text-rose-500 animate-pulse"></i>
                                @elseif(in_array($ext, ['xls', 'xlsx']))
                                    <i class="ph ph-file-xls text-xl text-emerald-600"></i>
                                @elseif(in_array($ext, ['png', 'jpg', 'jpeg']))
                                    <i class="ph ph-image text-xl text-blue-500"></i>
                                @else
                                    <i class="ph ph-file text-xl text-slate-400"></i>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span class="text-slate-800 dark:text-slate-200 font-black max-w-[150px] md:max-w-xs truncate" title="{{ $doc->nama_dokumen }}">{{ $doc->nama_dokumen }}</span>
                                <span class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-tighter">{{ strtoupper($ext) }} ASSET</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        @if($doc->pajak)
                            <div class="flex flex-col">
                                <a href="{{ route('pajak.show', $doc->pajak_id) }}" class="text-blue-600 dark:text-blue-400 font-black hover:underline mb-0.5 truncate max-w-[120px]">{{ $doc->pajak->nama_perusahaan }}</a>
                                <span class="text-slate-400 dark:text-slate-500 text-[8px] font-bold">{{ $doc->pajak->jenis_pajak }}</span>
                            </div>
                        @else
                            <span class="text-rose-300 dark:text-rose-900 italic font-black text-[8px] uppercase">UNDEFINED_SOURCE</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap hidden md:table-cell">
                        <span class="text-slate-500 dark:text-slate-300 font-black">{{ $doc->human_size }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                        <span class="text-slate-400 dark:text-slate-500 font-bold tabular-nums">{{ \Carbon\Carbon::parse($doc->tanggal_upload)->format('d/m/y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ $doc->file_url }}" target="_blank" class="p-2 text-slate-300 dark:text-slate-600 hover:text-blue-600 transition-all {{ $doc->is_remote ? 'bg-blue-50/50 rounded-lg' : '' }}" title="{{ $doc->is_remote ? 'Open API Link' : 'Download Local' }}">
                                <i class="ph {{ $doc->is_remote ? 'ph-arrow-square-out' : 'ph-download-simple' }} text-xl"></i>
                            </a>
                            <button @click="confirmDelete('{{ route('dokumen.destroy', $doc->id) }}', '{{ $doc->nama_dokumen }}')" class="p-2 text-slate-300 dark:text-slate-600 hover:text-rose-600 transition-all" title="Hapus Dokumen">
                                <i class="ph ph-trash text-xl"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-24 text-center grayscale opacity-50">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mb-2 border border-slate-100 dark:border-slate-800 shadow-inner">
                                <i class="ph ph-folder-open text-4xl text-slate-200 dark:text-slate-700"></i>
                            </div>
                            <div>
                                <h4 class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">VAULT_EMPTY_FAILSAFE</h4>
                                <p class="text-[8px] text-slate-300 dark:text-slate-600 mt-1 uppercase font-bold italic tracking-tighter">Gunakan menu detail pajak untuk mengunggah aset baru.</p>
                            </div>
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
            ROW: {{ $dokumens->firstItem() ?? 0 }}—{{ $dokumens->lastItem() ?? 0 }} 
            <span class="w-1 h-1 bg-slate-200 dark:bg-slate-700 rounded-full"></span>
            TOTAL: {{ $dokumens->total() }}
        </div>
        
        @if($dokumens->hasPages())
        <nav class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ $dokumens->previousPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ $dokumens->onFirstPage() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-left font-black"></i>
            </a>
            @foreach ($dokumens->getUrlRange(max(1, $dokumens->currentPage() - 1), min($dokumens->lastPage(), $dokumens->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all {{ $page == $dokumens->currentPage() ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $dokumens->nextPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ !$dokumens->hasMorePages() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-right font-black"></i>
            </a>
        </nav>
        @endif
    </div>
</div>
@endsection
