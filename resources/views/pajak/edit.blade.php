@extends('layouts.app')

@section('title', 'Koreksi Data Pajak')
@section('page-title', 'Ubah Data Pajak (Advanced)')

@section('actions')
    <a href="{{ route('pajak.show', $pajak->id) }}" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
        <i class="ph ph-arrow-left text-lg"></i> <span class="hidden sm:inline">BATAL / KEMBALI</span>
    </a>
@endsection

@section('content')
<div class="max-w-5xl mt-2 mx-auto" x-data="{ tab: 'profil' }">
    <form action="{{ route('pajak.update', $pajak->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Tab Navigation Area -->
        <div class="mb-6 flex flex-wrap gap-2 p-1.5 bg-white dark:bg-slate-800 rounded-[28px] border border-slate-100 dark:border-slate-700 shadow-sm sticky top-0 z-10 backdrop-blur-md bg-white/80 dark:bg-slate-800/80">
            <button type="button" @click="tab = 'profil'" :class="tab === 'profil' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="relative flex items-center gap-2 px-4 py-2.5 rounded-[22px] text-[9px] font-black uppercase tracking-widest transition-all leading-none shrink-0">
                <i class="ph ph-buildings text-lg"></i> Profil Entitas
                @if($errors->hasAny(['nama_perusahaan', 'status_perusahaan', 'tahun_berdiri', 'kategori_usaha', 'alamat_lengkap']))
                    <span class="absolute top-1 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white dark:border-slate-800 animate-pulse"></span>
                @endif
            </button>
            <button type="button" @click="tab = 'legal'" :class="tab === 'legal' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="relative flex items-center gap-2 px-4 py-2.5 rounded-[22px] text-[9px] font-black uppercase tracking-widest transition-all leading-none shrink-0">
                <i class="ph ph-certificate text-lg"></i> Detail Legalitas
                @if($errors->hasAny(['npwp', 'nib', 'no_akta', 'sk_kemenkumham', 'id_transaksi_source']))
                    <span class="absolute top-1 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white dark:border-slate-800 animate-pulse"></span>
                @endif
            </button>
            <button type="button" @click="tab = 'kontak'" :class="tab === 'kontak' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="relative flex items-center gap-2 px-4 py-2.5 rounded-[22px] text-[9px] font-black uppercase tracking-widest transition-all leading-none shrink-0">
                <i class="ph ph-users-square text-lg"></i> Kontak & PIC
                @if($errors->hasAny(['website', 'nama_pic', 'jabatan_pic', 'kontak_pic', 'email_pic']))
                    <span class="absolute top-1 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white dark:border-slate-800 animate-pulse"></span>
                @endif
            </button>
            <button type="button" @click="tab = 'pajak'" :class="tab === 'pajak' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="relative flex items-center gap-2 px-4 py-2.5 rounded-[22px] text-[9px] font-black uppercase tracking-widest transition-all leading-none shrink-0">
                <i class="ph ph-receipt text-lg"></i> Parameter Pajak
                @if($errors->hasAny(['jenis_pajak', 'jenis_pajak_rincian', 'periode', 'kpp_pratama']))
                    <span class="absolute top-1 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white dark:border-slate-800 animate-pulse"></span>
                @endif
            </button>
            <button type="button" @click="tab = 'status'" :class="tab === 'status' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="relative flex items-center gap-2 px-4 py-2.5 rounded-[22px] text-[9px] font-black uppercase tracking-widest transition-all leading-none shrink-0">
                <i class="ph ph-clock-countdown text-lg"></i> Timeline & Status
                @if($errors->hasAny(['tanggal_input', 'tanggal_jatuh_tempo', 'keterangan']))
                    <span class="absolute top-1 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white dark:border-slate-800 animate-pulse"></span>
                @endif
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-8 space-y-6">
                <!-- Tab: Profil Entitas -->
                <div x-show="tab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/10">
                        <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2 leading-none">
                            <i class="ph ph-identification-card text-blue-600 text-xl"></i> Edit Profil Entitas
                        </h3>
                        <span class="text-[8px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full uppercase">ID: {{ $pajak->id }}</span>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Nama Resmi Perusahaan / Institusi</label>
                            <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $pajak->nama_perusahaan) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border {{ $errors->has('nama_perusahaan') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-800' }} rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-100 outline-none transition-all" 
                                placeholder="Contoh: PT Angkasa Raya Indonesia Tbk.">
                            @error('nama_perusahaan') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Status Perusahaan</label>
                            <select name="status_perusahaan" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                                <option value="">Pilih Status...</option>
                                <option value="BUMN" {{ old('status_perusahaan', $pajak->status_perusahaan) == 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                                <option value="Swasta Nasional" {{ old('status_perusahaan', $pajak->status_perusahaan) == 'Swasta Nasional' ? 'selected' : '' }}>Swasta Nasional</option>
                                <option value="PMA" {{ old('status_perusahaan', $pajak->status_perusahaan) == 'PMA' ? 'selected' : '' }}>PMA (Penanaman Modal Asing)</option>
                                <option value="Yayasan" {{ old('status_perusahaan', $pajak->status_perusahaan) == 'Yayasan' ? 'selected' : '' }}>Yayasan / Institusi Publik</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Tahun Berdiri</label>
                            <input type="text" name="tahun_berdiri" value="{{ old('tahun_berdiri', $pajak->tahun_berdiri) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Ex: 2010">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Klasifikasi Sektor Bisnis</label>
                            <input type="text" name="kategori_usaha" value="{{ old('kategori_usaha', $pajak->kategori_usaha) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Contoh: Manufaktur, Perbankan, etc.">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Alamat Korespondensi Formal (Head Office)</label>
                            <textarea name="alamat_lengkap" rows="3" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-100 outline-none transition-all">{{ old('alamat_lengkap', $pajak->alamat_lengkap) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab: Detail Legalitas -->
                <div x-show="tab === 'legal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/10">
                        <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2 leading-none">
                            <i class="ph ph-certificate text-blue-600 text-xl"></i> Kelengkapan Legalitas & ID Resmi
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">NPWP Perusahaan</label>
                            <input type="text" name="npwp" value="{{ old('npwp', $pajak->npwp) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="00.000.000.0-000.000">
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Nomor Induk Berusaha (NIB)</label>
                            <input type="text" name="nib" value="{{ old('nib', $pajak->nib) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Masukkan 13 digit NIB...">
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Nomor Akta Pendirian</label>
                            <input type="text" name="no_akta" value="{{ old('no_akta', $pajak->no_akta) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Sesuai Akta Notaris terbaru...">
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">SK Kemenkumham (Pengesahan)</label>
                            <input type="text" name="sk_kemenkumham" value="{{ old('sk_kemenkumham', $pajak->sk_kemenkumham) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Nomor AHU-XXXXXXX...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">ID Transaksi / Data Source Referensi</label>
                            <input type="text" name="id_transaksi_source" value="{{ old('id_transaksi_source', $pajak->id_transaksi_source) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-black text-blue-600 dark:text-blue-400 outline-none" placeholder="Source ID dari sistem eksternal (optional)">
                        </div>
                    </div>
                </div>

                <!-- Tab: Kontak & PIC -->
                <div x-show="tab === 'kontak'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/10">
                        <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2 leading-none">
                            <i class="ph ph-users-square text-blue-600 text-xl"></i> Hubungan Korespondensi & PIC
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">URL Website Resmi</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 border border-r-0 border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 rounded-l-xl text-slate-400 text-[10px]">https://</span>
                                    <input type="text" name="website" value="{{ old('website', $pajak->website) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-r-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="www.perusahaan.com">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Nama Koordinator PIC</label>
                                <input type="text" name="nama_pic" value="{{ old('nama_pic', $pajak->nama_pic) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Nama lengkap...">
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Jabatan PIC</label>
                                <input type="text" name="jabatan_pic" value="{{ old('jabatan_pic', $pajak->jabatan_pic) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="Contoh: Tax Manager, Finance, etc.">
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Telepon / WhatsApp Aktif</label>
                                <input type="text" name="kontak_pic" value="{{ old('kontak_pic', $pajak->kontak_pic) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="08XXXXXXXXX">
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Electronic Mail (PIC)</label>
                                <input type="email" name="email_pic" value="{{ old('email_pic', $pajak->email_pic) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none" placeholder="pic@perusahaan.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Parameter Pajak -->
                <div x-show="tab === 'pajak'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/10">
                        <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2 leading-none">
                            <i class="ph ph-receipt text-blue-600 text-xl"></i> Parameter Objek & Kewajiban Pajak
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Kategori Utama Pajak</label>
                            <input type="text" name="jenis_pajak" value="{{ old('jenis_pajak', $pajak->jenis_pajak) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border {{ $errors->has('jenis_pajak') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-800' }} rounded-xl text-[11px] font-black text-slate-800 dark:text-slate-200 outline-none" placeholder="Ex: PPh Pasal 21, PPh Pasal 23, PPN...">
                            @error('jenis_pajak') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Rincian / Sub-Kategori Pajak (Optional)</label>
                            <input type="text" name="jenis_pajak_rincian" value="{{ old('jenis_pajak_rincian', $pajak->jenis_pajak_rincian) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Periode Masa Pajak</label>
                            <input type="text" name="periode" value="{{ old('periode', $pajak->periode) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border {{ $errors->has('periode') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-800' }} rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                            @error('periode') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Regional KPP Office (Terdaftar)</label>
                            <input type="text" name="kpp_pratama" value="{{ old('kpp_pratama', $pajak->kpp_pratama) }}" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-black text-blue-600 dark:text-blue-400 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Tab: Timeline & Status -->
                <div x-show="tab === 'status'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden" x-cloak>
                    <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-slate-50/10">
                        <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] flex items-center gap-2 leading-none">
                            <i class="ph ph-clock-countdown text-blue-600 text-xl"></i> Timeline Kepatuhan & Catatan
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Tanggal Data Diinput</label>
                                <input type="date" name="tanggal_input" value="{{ old('tanggal_input', $pajak->tanggal_input) }}" 
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border {{ $errors->has('tanggal_input') ? 'border-rose-500' : 'border-slate-100 dark:border-slate-800' }} rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">
                                @error('tanggal_input') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Batas Akhir (Deadline)</label>
                                <input type="date" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $pajak->tanggal_jatuh_tempo) }}" 
                                    class="w-full px-4 py-2.5 bg-rose-50/50 dark:bg-rose-950/20 border {{ $errors->has('tanggal_jatuh_tempo') ? 'border-rose-500' : 'border-rose-100 dark:border-rose-900' }} rounded-xl text-[11px] font-black text-rose-600 dark:text-rose-400 outline-none">
                                @error('tanggal_jatuh_tempo') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1">Catatan Internal / Keterangan Pendukung</label>
                            <textarea name="keterangan" rows="4" 
                                class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-[11px] font-bold text-slate-800 dark:text-slate-200 outline-none">{{ old('keterangan', $pajak->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Summary & Actions -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Data Update Sidebar -->
                <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                        <i class="ph ph-note-pencil text-8xl"></i>
                    </div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4">Update Verification</h4>
                    <div class="space-y-4 relative z-10">
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Terakhir Diperbarui</p>
                            <p class="text-[10px] font-bold">{{ $pajak->updated_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Saat Ini</p>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full {{ $pajak->status === 'selesai' ? 'bg-emerald-500' : ($pajak->status === 'terlambat' ? 'bg-rose-500' : 'bg-blue-600') }}">
                                {{ $pajak->status }}
                            </span>
                        </div>
                    </div>
                    <p class="mt-8 text-[8px] font-medium opacity-60 leading-relaxed uppercase tracking-widest">Setiap perubahan akan dicatat dalam riwayat audit sistem.</p>
                </div>

                <div class="p-2 space-y-4">
                    <button type="submit" class="w-full group relative flex items-center justify-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-3xl shadow-xl shadow-blue-200 dark:shadow-none transition-all duration-300 text-[10px] tracking-widest uppercase overflow-hidden">
                        <span class="relative z-10 flex items-center gap-3">
                            <i class="ph ph-floppy-disk text-xl"></i> KOMIT_PERUBAHAN
                        </span>
                    </button>
                    
                    <button type="button" @click="if(tab === 'profil') tab = 'legal'; else if(tab === 'legal') tab = 'kontak'; else if(tab === 'kontak') tab = 'pajak'; else if(tab === 'pajak') tab = 'status';" class="w-full py-3 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-400 font-black rounded-2xl text-[9px] uppercase tracking-widest hover:text-blue-600 transition-all flex items-center justify-center gap-2">
                        Bagian Selanjutnya <i class="ph ph-arrow-right"></i>
                    </button>

                    <div class="flex items-center gap-2 justify-center py-2">
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="tab === 'profil' ? 'bg-blue-600 scale-150' : 'bg-slate-300'"></div>
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="tab === 'legal' ? 'bg-blue-600 scale-150' : 'bg-slate-300'"></div>
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="tab === 'kontak' ? 'bg-blue-600 scale-150' : 'bg-slate-300'"></div>
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="tab === 'pajak' ? 'bg-blue-600 scale-150' : 'bg-slate-300'"></div>
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="tab === 'status' ? 'bg-blue-600 scale-150' : 'bg-slate-300'"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
