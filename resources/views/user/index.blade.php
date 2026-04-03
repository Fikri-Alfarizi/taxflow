@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('page-title', 'Kontrol Administrasi Sistem')

@section('actions')
    <a href="{{ route('user.create') }}" class="flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[10px] font-black hover:bg-blue-700 transition-all shadow-md dark:shadow-none">
        <i class="ph ph-user-plus text-lg"></i> <span class="hidden md:inline uppercase tracking-widest leading-none mt-0.5">Tambah Pengguna Baru</span>
    </a>
@endsection

@section('content')
<!-- High-Density Search & Filter Header -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 mb-6 transition-all mt-2">
    <form action="{{ route('user.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-4">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5 leading-none">Pencarian Universal</label>
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="w-full pl-9 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-50 dark:focus:ring-blue-900/20 focus:bg-white dark:focus:bg-slate-900 transition-all">
            </div>
        </div>

        <div class="md:col-span-2 hidden md:block">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5 leading-none">Hak Akses</label>
            <div class="relative">
                <select name="role" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all appearance-none uppercase">
                    <option value="">SEMUA PERAN</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>ADMINISTRATOR</option>
                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>STAFF_TAX</option>
                </select>
                <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>

        <div class="md:col-span-2 hidden md:block">
            <label class="block text-[8px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-[0.2em] mb-1.5 leading-none">Status Akun</label>
            <div class="relative">
                <select name="status" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 focus:outline-none transition-all appearance-none uppercase">
                    <option value="">SEMUA STATUS</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>AKTIF</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>SUSPENDED</option>
                </select>
                <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="flex-1 px-4 py-1.5 bg-slate-800 dark:bg-slate-700 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md">Terapkan Filter</button>
            <a href="{{ route('user.index') }}" wire:navigate class="px-4 py-1.5 bg-slate-100 dark:bg-slate-900 text-slate-400 dark:text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-800 transition-all text-center flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- User List Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-900/50 text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <th class="px-6 py-4 text-left">Informasi Pengguna</th>
                    <th class="px-6 py-4 text-left hidden lg:table-cell">Kontak Digital</th>
                    <th class="px-6 py-4 text-center">Status Sesi</th>
                    <th class="px-6 py-4 text-center hidden md:table-cell">Akses Ganda</th>
                    <th class="px-6 py-4 text-right">Opsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700 uppercase text-[9px] tracking-wide font-black text-slate-600 dark:text-slate-400">
                @foreach($users as $u)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-all border-l-2 border-l-transparent hover:border-l-blue-600">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600 shadow-sm transition-colors text-xs">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-slate-800 dark:text-slate-200 font-black leading-none mb-1">{{ $u->name }}</span>
                                <span class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">ID: #US-{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 lowercase normal-case italic hidden lg:table-cell">{{ $u->email }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($u->status_aktif)
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900 rounded font-black text-[8px] transition-all">
                                <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse"></span> AKTIF
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-600 border border-slate-100 dark:border-slate-700 rounded font-black text-[8px] transition-all uppercase">
                                SUSPENDED
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap hidden md:table-cell">
                        @if($u->role === 'admin')
                            <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 rounded-md font-black transition-all">ADMIN</span>
                        @else
                            <span class="px-2 py-0.5 bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-700 rounded-md font-black transition-all">STAFF_TAX</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap font-bold">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('user.edit', $u->id) }}" wire:navigate class="p-1 text-slate-300 dark:text-slate-600 hover:text-amber-500 transition-all">
                                <i class="ph ph-pencil-simple text-lg"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <button @click="confirmDelete('{{ route('user.destroy', $u->id) }}', '{{ $u->name }}')" class="p-1 text-slate-300 dark:text-slate-600 hover:text-rose-500 transition-all">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                            @else
                            <div class="p-1 opacity-20 cursor-not-allowed">
                                <i class="ph ph-lock-key text-lg"></i>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- High-Performance Professional Pagination Footer -->
    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex items-center gap-2">
            ROW: {{ $users->firstItem() ?? 0 }}—{{ $users->lastItem() ?? 0 }} 
            <span class="w-1 h-1 bg-slate-200 dark:bg-slate-700 rounded-full"></span>
            TOTAL: {{ $users->total() }}
        </div>
        
        @if($users->hasPages())
        <nav class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ $users->previousPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ $users->onFirstPage() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-left font-black"></i>
            </a>
            @foreach ($users->getUrlRange(max(1, $users->currentPage() - 1), min($users->lastPage(), $users->currentPage() + 1)) as $page => $url)
                <a href="{{ $url }}" wire:navigate class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black transition-all {{ $page == $users->currentPage() ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    {{ $page }}
                </a>
            @endforeach
            <a href="{{ $users->nextPageUrl() }}" wire:navigate class="w-8 h-8 rounded-lg border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all {{ !$users->hasMorePages() ? 'opacity-20 pointer-events-none' : '' }}">
                <i class="ph ph-caret-right font-black"></i>
            </a>
        </nav>
        @endif
    </div>
@endsection
