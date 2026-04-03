@extends('layouts.app')

@section('title', 'Registrasi Data Pajak')
@section('page-title', 'Tambah Data Pajak (Draft)')

@section('actions')
    <a href="{{ route('pajak.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
        <i class="ph ph-arrow-left text-lg"></i> <span class="hidden sm:inline">KEMBALI KE DAFTAR</span>
    </a>
@endsection

@section('content')
<div class="max-w-5xl mt-2 mx-auto">
    <form action="{{ route('pajak.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            
            <!-- Left: Primary Identity DNA (8 Columns) -->
            <div class="lg:col-span-8 space-y-4 order-2 lg:order-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-file-plus text-blue-600 text-lg"></i> Entri Identifikasi Entitas
                        </h3>
                    </div>
                    
                    <div class="p-5 lg:p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="md:col-span-2">
                            <label for="nama_perusahaan" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Nama Perusahaan / Institusi</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" required value="{{ old('nama_perusahaan') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all shadow-sm" 
                                placeholder="Contoh: PT Angkasa Raya Indonesia">
                            @error('nama_perusahaan') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="npwp" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">NPWP (ID Resmi)</label>
                            <input type="text" name="npwp" id="npwp" value="{{ old('npwp') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="00.000.000.0-000.000">
                        </div>

                        <div>
                            <label for="kategori_usaha" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Sektor Bisnis</label>
                            <input type="text" name="kategori_usaha" id="kategori_usaha" value="{{ old('kategori_usaha') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="Contoh: Perbankan">
                        </div>

                        <div class="md:col-span-2">
                            <label for="alamat_lengkap" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Alamat Korespondensi Formal</label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="2" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all"
                                placeholder="Tuliskan alamat lengkap kantor pusat..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-users text-blue-600 text-lg"></i> Konfigurasi Penanggung Jawab (PIC)
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label for="nama_pic" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Nama Koordinator PIC</label>
                            <input type="text" name="nama_pic" id="nama_pic" value="{{ old('nama_pic') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="Nama lengkap PIC...">
                        </div>
                        <div>
                            <label for="kontak_pic" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Telepon / WhatsApp</label>
                            <input type="text" name="kontak_pic" id="kontak_pic" value="{{ old('kontak_pic') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="08XXXXXXXXX">
                        </div>
                        <div class="md:col-span-2">
                            <label for="email_pic" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Mail Korespondensi</label>
                            <input type="email" name="email_pic" id="email_pic" value="{{ old('email_pic') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="nama@perusahaan.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Tax Parameters & Metadata (4 Columns) -->
            <div class="lg:col-span-4 space-y-4 order-1 lg:order-2">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-article text-blue-600 text-lg"></i> Parameter Objek Pajak
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6 space-y-4">
                        <div>
                            <label for="jenis_pajak" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Kategori Pajak</label>
                            <input type="text" name="jenis_pajak" id="jenis_pajak" required value="{{ old('jenis_pajak') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-black text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="Ex: PPh Pasal 21">
                        </div>
                        <div>
                            <label for="periode" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Periode Masa</label>
                            <input type="text" name="periode" id="periode" required value="{{ old('periode') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all" placeholder="Ex: March 2026">
                        </div>
                        <div>
                            <label for="kpp_pratama" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Regional KPP Office</label>
                            <input type="text" name="kpp_pratama" id="kpp_pratama" value="{{ old('kpp_pratama') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-black text-blue-600 dark:text-blue-400 focus:outline-none transition-all" placeholder="Ex: KPP Pratama Gambir">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-calendar-plus text-blue-600 text-lg"></i> Siklus Kepatuhan
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6 space-y-4">
                        <div>
                            <label for="tanggal_input" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Tanggal Input</label>
                            <input type="date" name="tanggal_input" id="tanggal_input" required value="{{ old('tanggal_input', date('Y-m-d')) }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label for="tanggal_jatuh_tempo" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Batas Deadline</label>
                            <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" required value="{{ old('tanggal_jatuh_tempo') }}" 
                                class="w-full px-3 py-2 bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-lg text-[11px] font-black text-rose-600 dark:text-rose-400 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-3 bg-slate-800 dark:bg-slate-700 hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-100 dark:shadow-none transition-all text-[10px] tracking-widest uppercase">
                        <i class="ph ph-check-circle text-xl"></i> REGISTRASI_DATA_NEW
                    </button>
                    <p class="text-center text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mt-4">Data draf akan disimpan secara permanen ke sistem</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
