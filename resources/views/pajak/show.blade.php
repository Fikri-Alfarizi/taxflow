@use('Illuminate\Support\Facades\Storage')
@extends('layouts.app')

@section('title', 'Detail Pajak')
@section('page-title', 'Detail & Progres Pajak')

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('pajak.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <i class="ph ph-arrow-left text-lg"></i> <span class="hidden sm:inline">KEMBALI</span>
        </a>
        <a href="{{ route('pajak.edit', $pajak->id) }}" class="flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[10px] font-black hover:bg-blue-700 transition-all shadow-md">
            <i class="ph ph-pencil-simple text-lg"></i> <span class="hidden sm:inline">EDIT MASTER</span>
        </a>
    </div>
@endsection

@section('content')
<div x-data="{ 
    activeTab: '{{ session('active_tab', 'overview') }}',
    logoError: false 
}" class="space-y-4 mt-2">
    
    <!-- Top Analytical Header (Segmented Menu) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-3 lg:p-4 flex flex-col items-center justify-between gap-4 transition-all duration-500">
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-2xl bg-white dark:bg-slate-700 border border-slate-100 dark:border-slate-600 flex items-center justify-center overflow-hidden shrink-0 transition-colors">
                @if($pajak->logo_url)
                    <img src="{{ $pajak->logo_url }}" alt="{{ $pajak->nama_perusahaan }}" class="w-full h-full object-contain p-1" x-show="!logoError" x-on:error="logoError = true">
                    <span x-show="logoError" x-cloak class="text-base font-black text-slate-300">{{ substr($pajak->nama_perusahaan, 0, 1) }}</span>
                @else
                    <span class="text-base font-black text-slate-300">{{ substr($pajak->nama_perusahaan, 0, 1) }}</span>
                @endif
            </div>
            <div class="text-center md:text-left overflow-hidden w-full md:w-auto">
                <h2 class="text-[12px] lg:text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight leading-none mb-1 truncate px-4 md:px-0">{{ $pajak->nama_perusahaan }}</h2>
                <div class="flex items-center justify-center md:justify-start gap-2">
                    <span class="text-[8px] lg:text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">NPWP: {{ $pajak->npwp ?? '-' }}</span>
                    <span class="w-1 h-1 bg-slate-200 dark:bg-slate-700 rounded-full"></span>
                    <span class="text-[8px] lg:text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ $pajak->jenis_pajak }}</span>
                </div>
            </div>
        </div>

        <!-- High-Density Tab Switcher (Scrollable on Mobile) -->
        <div class="w-full lg:w-auto overflow-x-auto scrollbar-hide py-1">
            <div class="flex items-center bg-slate-50 dark:bg-slate-900 p-1 rounded-xl border border-slate-100 dark:border-slate-700 min-w-max">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-100 dark:border-slate-700/50' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'" class="px-3 md:px-4 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Ringkasan</button>
                <button @click="activeTab = 'dna'" :class="activeTab === 'dna' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-100 dark:border-slate-700/50' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'" class="px-3 md:px-4 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Profil & Legal</button>
                <button @click="activeTab = 'vault'" :class="activeTab === 'vault' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-100 dark:border-slate-700/50' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'" class="px-3 md:px-4 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all whitespace-nowrap">Brankas Digital ({{ $pajak->dokumens->count() }})</button>
                <button @click="activeTab = 'audit'" :class="activeTab === 'audit' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-100 dark:border-slate-700/50' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600'" class="px-3 md:px-4 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Jejak Audit</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Main Content Area -->
        <div class="lg:col-span-8 order-2 lg:order-1">
            
            <!-- Tab 1: Overview Dashboard -->
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
                        <p class="text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mb-2">Status Kepatuhan</p>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full border-2 border-slate-100 dark:border-slate-900 border-t-blue-500 {{ $pajak->status !== 'selesai' ? 'animate-spin' : '' }}"></div>
                            <div>
                                <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase leading-none">{{ strtoupper($pajak->status) }}</p>
                                <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5">Siklus Aktif</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
                        <p class="text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mb-2">Kedekatan Jatuh Tempo</p>
                        <p class="text-[10px] font-black {{ $pajak->sisa_hari < 0 ? 'text-rose-500' : 'text-amber-500' }} uppercase leading-none">{{ $pajak->sisa_hari_text }}</p>
                        <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5">{{ \Carbon\Carbon::parse($pajak->tanggal_jatuh_tempo)->format('d F Y') }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-colors">
                        <p class="text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mb-2">Integritas Data</p>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase leading-none">VERIFIED_SECURE</p>
                        <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5 truncate tracking-tighter">Source: {{ $pajak->id_transaksi_source ?? 'LOCAL_STORAGE' }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="p-4 border-b border-slate-50 dark:border-slate-700/50 flex bg-slate-50/10 dark:bg-slate-900/10">
                        <h4 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Catatan Strategis Sistem</h4>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                        <div>
                            <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mb-2">Keterangan Operasional</label>
                            <p class="text-[10px] md:text-[11px] font-bold text-slate-600 dark:text-slate-400 leading-relaxed italic">"{{ $pajak->keterangan ?: 'Tidak ada catatan naratif untuk periode ini.' }}"</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700 transition-colors">
                             <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mb-2">Statistik Vault</label>
                             <div class="space-y-2.5">
                                 <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                     <span class="text-slate-400 tracking-tighter">Dokumen Terarsip</span>
                                     <span class="text-slate-800 dark:text-slate-200 tabular-nums">{{ $pajak->dokumens->count() }} Files</span>
                                 </div>
                                 <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                     <span class="text-slate-400 tracking-tighter">Total Volume Aset</span>
                                     <span class="text-slate-800 dark:text-slate-200 tabular-nums">{{ $pajak->dokumens->sum('ukuran_file') ? round($pajak->dokumens->sum('ukuran_file')/1024, 2).' KB' : '0 B' }}</span>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Entity DNA (Extensive Profile) -->
            <div x-show="activeTab === 'dna'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4" x-cloak>
                <!-- Profile Section -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-50 dark:border-slate-700 bg-blue-50/5 dark:bg-slate-900/20">
                        <h4 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">DNA Profil & Legalitas Korporasi</h4>
                    </div>
                    <div class="p-6 lg:p-8 space-y-8">
                        <!-- Group 1: General Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">Status Legal</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase">{{ $pajak->status_perusahaan ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">Tahun Berdiri</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 tabular-nums">{{ $pajak->tahun_berdiri ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">Website</label>
                                <p class="text-[10px] font-black text-blue-600 lowercase">{{ $pajak->website ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Group 2: Key IDs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-slate-50 dark:border-slate-700/50">
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">NPWP</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 tabular-nums tracking-tighter">{{ $pajak->npwp ?? 'NOT_SET' }}</p>
                            </div>
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">NIB (13 Digit)</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 tabular-nums tracking-tighter">{{ $pajak->nib ?? 'NOT_SET' }}</p>
                            </div>
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">No. Akta Pendirian</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase truncate" title="{{ $pajak->no_akta }}">{{ $pajak->no_akta ?? 'NOT_SET' }}</p>
                            </div>
                            <div>
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">SK Kemenkumham</label>
                                <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase truncate" title="{{ $pajak->sk_kemenkumham }}">{{ $pajak->sk_kemenkumham ?? 'NOT_SET' }}</p>
                            </div>
                        </div>

                        <!-- Group 3: Location & PIC -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pt-6 border-t border-slate-50 dark:border-slate-700/50">
                            <div class="md:col-span-7">
                                <label class="block text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5">Alamat Korespondensi Formal</label>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 leading-relaxed uppercase">{{ $pajak->alamat_lengkap ?? 'NO_OFFICIAL_ADDRESS_DATA' }}</p>
                            </div>
                            <div class="md:col-span-5 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-3">Koordinator Penanggung Jawab</p>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                        <span class="text-slate-400 tracking-tighter">Nama PIC</span>
                                        <span class="text-slate-800 dark:text-slate-200 truncate ml-2">{{ $pajak->nama_pic ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                        <span class="text-slate-400 tracking-tighter">Jabatan</span>
                                        <span class="text-slate-800 dark:text-slate-200 truncate ml-2 text-[8px]">{{ $pajak->jabatan_pic ?? 'Staff' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                        <span class="text-slate-400 tracking-tighter">Kontak aktif</span>
                                        <span class="text-slate-800 dark:text-slate-200 tabular-nums ml-2">{{ $pajak->kontak_pic ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                                        <span class="text-slate-400 tracking-tighter">Mails</span>
                                        <span class="text-slate-800 dark:text-slate-200 lowercase ml-2 truncate">{{ $pajak->email_pic ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Digital Vault -->
            <div x-show="activeTab === 'vault'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <h4 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Arsip Digital Aset Pajak</h4>
                        <button @click="$refs.uploadForm.classList.toggle('hidden')" class="text-[8px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest hover:underline">UNGGAH ASSET +</button>
                    </div>

                    <div x-ref="uploadForm" class="{{ $errors->has('nama_dokumen') || $errors->has('file_dokumen') ? '' : 'hidden' }} p-5 lg:p-6 bg-blue-50/5 dark:bg-slate-900/20 border-b border-slate-100 dark:border-slate-700 transition-all">
                        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4 items-end">
                            @csrf
                            <input type="hidden" name="pajak_id" value="{{ $pajak->id }}">
                            <div class="w-full md:flex-1">
                                <label class="block text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5 leading-none">Label Referensi</label>
                                <input type="text" name="nama_dokumen" value="{{ old('nama_dokumen') }}" required placeholder="Cth: Bukti Lapor PPh" class="w-full px-3 py-1.5 bg-white dark:bg-slate-800 border {{ $errors->has('nama_dokumen') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-700' }} rounded-lg text-[10px] font-bold focus:outline-none transition-all transition-colors">
                                @error('nama_dokumen')
                                    <p class="text-[8px] font-bold text-rose-500 mt-1 uppercase tracking-tighter">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-full md:flex-1">
                                <label class="block text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5 leading-none">Binary Aset</label>
                                <input type="file" name="file_dokumen" required class="w-full text-slate-400 text-[10px] file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-[8px] file:font-black file:bg-slate-50 dark:file:bg-slate-700 file:text-slate-600 dark:file:text-slate-300 hover:file:bg-slate-100 dark:hover:file:bg-slate-600 transition-all">
                                @error('file_dokumen')
                                    <p class="text-[8px] font-bold text-rose-500 mt-1 uppercase tracking-tighter">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-2 bg-slate-800 dark:bg-slate-700 text-white rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md">UNGGAH</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto scrollbar-hide">
                        <table class="w-full text-[10px]">
                            <thead>
                                <tr class="bg-slate-50/20 dark:bg-slate-900/20 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                                    <th class="px-5 py-3 text-left">Aset Type</th>
                                    <th class="px-5 py-3 text-left">Digital Label</th>
                                    <th class="px-5 py-3 text-center hidden sm:table-cell">Ukuran</th>
                                    <th class="px-5 py-3 text-right">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-bold uppercase tracking-tight text-slate-600 dark:text-slate-400 transition-all">
                                @forelse($pajak->dokumens->sortByDesc('created_at') as $doc)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        @php $ext = pathinfo($doc->file_dokumen, PATHINFO_EXTENSION); @endphp
                                        <span class="px-1.5 py-0.5 rounded bg-white dark:bg-slate-700 border border-slate-100 dark:border-slate-600 text-[8px] font-black text-slate-400 font-mono">{{ strtoupper($ext) }}</span>
                                    </td>
                                    <td class="px-5 py-3 truncate max-w-[120px] md:max-w-xs text-slate-800 dark:text-slate-200">{{ $doc->nama_dokumen }}</td>
                                    <td class="px-5 py-3 text-center whitespace-nowrap tabular-nums hidden sm:table-cell text-[9px]">{{ $doc->human_size }}</td>
                                    <td class="px-5 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ $doc->file_url }}" target="_blank" class="p-1.5 {{ $doc->is_remote ? 'text-blue-600' : 'text-slate-400' }} hover:scale-110 transition-all font-black text-[9px]" title="{{ $doc->is_remote ? 'Open API' : 'Download' }}">
                                                <i class="ph {{ $doc->is_remote ? 'ph-arrow-square-out' : 'ph-download-simple' }} text-base"></i>
                                            </a>
                                            <button @click="confirmDelete('{{ route('dokumen.destroy', $doc->id) }}', '{{ $doc->nama_dokumen }}')" class="p-1.5 text-slate-300 dark:text-slate-600 hover:text-rose-600 transition-all"><i class="ph ph-trash text-base"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-300 dark:text-slate-700 italic font-black uppercase tracking-[0.2em] text-[10px]">VAULT_EMPTY</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Audit Trail -->
            <div x-show="activeTab === 'audit'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="p-4 bg-slate-50/10 dark:bg-slate-900/10 border-b border-slate-50 dark:border-slate-700">
                        <h4 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Kronologi Jejak Telemetri Audit</h4>
                    </div>
                    <div class="p-6 lg:p-10 relative">
                        <div class="absolute left-[38.5px] lg:left-[54.5px] top-10 bottom-16 w-px bg-slate-50 dark:bg-slate-700"></div>
                        <div class="space-y-6 relative z-10">
                            @forelse($pajak->monitorings->sortByDesc('created_at') as $log)
                            <div class="flex gap-4 lg:gap-8">
                                <div class="shrink-0 w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center ring-[3px] ring-white dark:ring-slate-800 transition-all
                                    {{ $log->status_proses == 'selesai' ? 'text-emerald-500' : ($log->status_proses == 'terlambat' ? 'text-rose-500' : 'text-amber-500') }}">
                                    <i class="ph {{ $log->status_proses == 'selesai' ? 'ph-check-circle' : ($log->status_proses == 'terlambat' ? 'ph-warning-circle' : 'ph-arrows-clockwise') }} text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-[8px] lg:text-[9px] font-black uppercase tracking-[0.2em] {{ $log->status_proses == 'selesai' ? 'text-emerald-600' : ($log->status_proses == 'terlambat' ? 'text-rose-600' : 'text-amber-500') }}">{{ strtoupper($log->status_proses) }}</p>
                                        <span class="text-[8px] text-slate-300 dark:text-slate-600 font-black tabular-nums tracking-tighter">{{ \Carbon\Carbon::parse($log->tanggal_update)->format('d M y/H:i') }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 italic tracking-tight leading-relaxed max-w-xl pr-4">"{{ $log->catatan ?: 'UPDATE_SYSTEM_AUTO_LOG' }}"</p>
                                    <div class="flex items-center gap-1.5 mt-2 transition-opacity">
                                        <div class="w-4 h-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-sm flex items-center justify-center text-[7px] font-black text-slate-400 uppercase leading-none">{{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}</div>
                                        <span class="text-[8px] font-black text-slate-300 dark:text-slate-500 lowercase tracking-tighter">{{ $log->user->name ?? 'SYS_DAEMON' }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-10 text-slate-100 dark:text-slate-800 text-[10px] font-black uppercase tracking-[0.4em] italic">NULL_TRACE</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Sidebar Control (4 Columns) -->
        <div class="lg:col-span-4 space-y-4 order-1 lg:order-2 sticky top-4 h-fit">
            <!-- Progress Control -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                <div class="p-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    <h5 class="text-[9px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Workflow Perintah Kerja</h5>
                    <i class="ph ph-command text-slate-300 dark:text-slate-600"></i>
                </div>
                <div class="p-6">
                    <form action="{{ route('monitoring.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="pajak_id" value="{{ $pajak->id }}">
                        <div>
                            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5 px-1 leading-none">Update Status Penugasan</label>
                            <div class="relative">
                                <select name="status_proses" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-black text-slate-800 dark:text-slate-200 focus:outline-none transition-all appearance-none shadow-sm uppercase">
                                    <option value="diproses" {{ $pajak->status == 'diproses' ? 'selected' : '' }}>PROSES (ON-GOING)</option>
                                    <option value="selesai" {{ $pajak->status == 'selesai' ? 'selected' : '' }}>SELESAI (STABIL)</option>
                                    <option value="terlambat" {{ $pajak->status == 'terlambat' ? 'selected' : '' }}>TERLAMBAT (KRITIS)</option>
                                </select>
                                <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-1.5 px-1 leading-none">Otorisasi Catatan Entri</label>
                            <textarea name="catatan" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-lg text-[10px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all placeholder:text-[9px] placeholder:italic" placeholder="Tulis catatan telemetri atau progres..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-slate-800 dark:bg-slate-700 hover:bg-black text-white font-black py-2.5 rounded-lg text-[9px] tracking-[0.2em] uppercase shadow-lg shadow-slate-100 dark:shadow-none transition-all">
                            KOMIT UPDATE STATUS →
                        </button>
                    </form>
                </div>
            </div>

            <!-- Objek Pajak Header -->
            <div class="bg-blue-600 rounded-xl p-5 text-white shadow-xl shadow-blue-200 dark:shadow-none">
                 <p class="text-[7px] font-black uppercase tracking-[0.3em] mb-1.5 opacity-70">Objek Pajak Utama</p>
                 <h4 class="text-xs font-black uppercase mb-3">{{ $pajak->jenis_pajak }}</h4>
                 <div class="space-y-2">
                     <div class="flex justify-between items-center text-[9px] font-black uppercase">
                         <span class="opacity-60">Periode Masa</span>
                         <span>{{ $pajak->periode }}</span>
                     </div>
                     <div class="flex justify-between items-center text-[9px] font-black uppercase">
                         <span class="opacity-60">KPP Regional</span>
                         <span class="truncate ml-2">{{ $pajak->kpp_pratama ?? 'N/A' }}</span>
                     </div>
                 </div>
            </div>

            <!-- Meta Data (High Density) -->
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 p-5 space-y-4 transition-colors">
                <h5 class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest border-b border-slate-50 dark:border-slate-700 pb-2 leading-none">Metadata Siklus Hidup</h5>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                        <span class="text-slate-400 tracking-tighter">Otorisator</span>
                        <span class="text-slate-800 dark:text-slate-300 truncate ml-2">{{ $pajak->user->name ?? 'SYSTEM' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[9px] font-black uppercase">
                        <span class="text-slate-400 tracking-tighter">Telemetri Terakhir</span>
                        <span class="text-slate-800 dark:text-slate-300 tabular-nums ml-2 font-mono">{{ $pajak->updated_at->format('d/m/y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
