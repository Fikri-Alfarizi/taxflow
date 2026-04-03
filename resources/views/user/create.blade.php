@extends('layouts.app')

@section('title', 'Registrasi Pengguna')
@section('page-title', 'Pendaftaran Akun Sistem Baru')

@section('actions')
    <a href="{{ route('user.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg text-[10px] font-black hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
        <i class="ph ph-arrow-left text-lg"></i> <span class="hidden sm:inline">KEMBALI KE DAFTAR</span>
    </a>
@endsection

@section('content')
<div class="max-w-5xl mt-2 mx-auto">
    <form action="{{ route('user.store') }}" method="POST" class="space-y-4">
    @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            
            <!-- Left: Recruitment Identity & Security (8 Columns) -->
            <div class="lg:col-span-8 space-y-4 order-2 lg:order-1">
                
                <!-- Card: New User Identity -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10 flex items-center justify-between">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-user-plus text-blue-600 text-lg"></i> Profil Identitas Baru
                        </h3>
                        <span class="text-[7px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded-full uppercase tracking-widest leading-none">Registration</span>
                    </div>
                    
                    <div class="p-5 lg:p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Nama Lengkap & Gelar Korporasi</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 transition-all shadow-sm"
                                placeholder="Contoh: Dr. Budi Santoso, M.Ak">
                            @error('name') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="email" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Alamat Surel Resmi (Email)</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-medium text-slate-800 dark:text-slate-200 focus:outline-none transition-all"
                                placeholder="nama@perusahaan.com">
                            @error('email') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card: Security & Initial Permissions -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-shield-check text-blue-600 text-lg"></i> Kredensial Keamanan Sistem
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="md:col-span-2">
                            <label for="role" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Hak Akses / Peran Sistem</label>
                            <div class="relative">
                                <select name="role" id="role" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-black text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/20 appearance-none transition-all uppercase">
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>STAF PERPAJAKAN</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                                </select>
                                <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Tetapkan Password Akses</label>
                            <input type="password" name="password" id="password" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all font-mono shadow-sm" placeholder="••••••••">
                            @error('password') <p class="mt-1 text-[8px] text-rose-600 font-bold px-1 uppercase tracking-tighter">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 px-1 leading-none">Konfirmasi Password Akses</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all font-mono shadow-sm" placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Account Initialization (4 Columns) -->
            <div class="lg:col-span-4 space-y-4 order-1 lg:order-2">
                
                <!-- Card: Initial Status -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-rocket text-blue-600 text-lg"></i> Inisialisasi Akun
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6">
                        <label class="block text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3 px-1 leading-none">Status Kepegawaian Awal</label>
                        <div class="space-y-2.5">
                            <label class="block cursor-pointer">
                                <input type="radio" name="status_aktif" value="1" {{ old('status_aktif', '1') == '1' ? 'checked' : '' }} class="peer hidden">
                                <div class="px-4 py-3 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 font-black text-[9px] uppercase tracking-[0.2em] flex items-center justify-between peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-100/10 peer-checked:border-emerald-200 dark:peer-checked:border-emerald-900 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-400 transition-all">
                                    PERSONEL AKTIF
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700 peer-checked:bg-emerald-500 transition-all border-2 border-white dark:border-slate-900 shadow-sm"></div>
                                </div>
                            </label>
                            
                            <label class="block cursor-pointer">
                                <input type="radio" name="status_aktif" value="0" {{ old('status_aktif') == '0' ? 'checked' : '' }} class="peer hidden">
                                <div class="px-4 py-3 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 font-black text-[9px] uppercase tracking-[0.2em] flex items-center justify-between peer-checked:bg-rose-50 dark:peer-checked:bg-rose-100/10 peer-checked:border-rose-200 dark:peer-checked:border-rose-900 peer-checked:text-rose-700 dark:peer-checked:text-rose-400 transition-all">
                                    SUSPENDED
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700 peer-checked:bg-rose-500 transition-all border-2 border-white dark:border-slate-900 shadow-sm"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Card: Security Recommendations -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/20 dark:bg-slate-900/10">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-widest leading-none">
                            <i class="ph ph-lock-key text-blue-600 text-lg"></i> Protokol Keamanan
                        </h3>
                    </div>
                    <div class="p-5 lg:p-6">
                        <ul class="text-[8px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-tight space-y-2">
                            <li class="flex gap-2 items-center"><span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span> Password minimal 8 karakter</li>
                            <li class="flex gap-2 items-center"><span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span> Email korporasi wajib unik</li>
                            <li class="flex gap-2 items-center"><span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span> Hak akses tervalidasi sistem</li>
                        </ul>
                    </div>
                </div>

                <div class="pt-4 px-1">
                    <button type="submit" class="w-full flex items-center justify-center gap-3 bg-slate-800 dark:bg-slate-700 hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-100 dark:shadow-none transition-all text-[11px] tracking-widest uppercase">
                        <i class="ph ph-user-plus text-xl"></i> REGISTER_NEW_USER
                    </button>
                    <p class="text-center text-[7px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.2em] mt-4 italic">Otorisasi ID akan divalidasi oleh sistem utama</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
