<!DOCTYPE html>
<html lang="id" x-data="{ 
    isDark: localStorage.getItem('isDark') === 'true',
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
    mobileSidebar: false,
    showDeleteModal: false,
    deleteUrl: '',
    deleteTitle: '',
    toggleTheme() {
        this.isDark = !this.isDark;
        localStorage.setItem('isDark', this.isDark);
        document.documentElement.classList.toggle('dark', this.isDark);
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: this.isDark } }));
    },
    toggleSidebar() {
        if (window.innerWidth < 1024) {
            this.mobileSidebar = !this.mobileSidebar;
        } else {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    },
    confirmDelete(url, title = 'Data ini akan dihapus permanen') {
        this.deleteUrl = url;
        this.deleteTitle = title;
        this.showDeleteModal = true;
    },
    executeDelete() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.deleteUrl;
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
        const method = document.createElement('input');
        method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
        form.appendChild(csrf); form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}" x-init="document.documentElement.classList.toggle('dark', isDark); window.confirmDelete = (url, title) => confirmDelete(url, title);">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaxFlow') - TaxFlow Professional</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('logo/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('logo/favicon/favicon.ico') }}">
    
    <!-- Apply dark mode BEFORE any content renders - prevent flash -->
    <script>
        (function() {
            const isDark = localStorage.getItem('isDark') === 'true' || 
                (!('isDark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>
        /* Apply immediately before body renders */
        html.dark { background-color: #0f172a; }
        html:not(.dark) { background-color: #f8fafc; }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0f172a',
                            surface: '#1e293b',
                            border: '#334155'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="sha384-9ndCyUa6z6j9i6Bqa8WOKgYm7+z1D1TqT0n9tYfXc1xOd4JnP7IxOy4YTZP1Yd0h" crossorigin="anonymous">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    @livewireStyles
    @yield('head')
    
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 11px; }
        [x-cloak] { display: none !important; }
        * {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        *::-webkit-scrollbar { display: none; }
        
        /* Force dark mode background - no gap */
        html.dark, html.dark body {
            background-color: #0f172a !important;
            min-height: 100vh;
        }
        html:not(.dark), html:not(.dark) body {
            background-color: #f8fafc !important;
            min-height: 100vh;
        }
        
        /* Smooth transitions but exclude body background */
        body, body * {
            transition: border-color 0.3s, color 0.3s, opacity 0.3s;
        }
        
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Global font size for consistency */
        body {
            font-size: 10px;
        }
        .text-base {
            font-size: 0.875rem; /* 14px */
        }
        .text-sm {
            font-size: 0.75rem; /* 12px */
        }
        .text-xs {
            font-size: 0.625rem; /* 10px */
        }
        /* Custom small text classes */
        .text-[8px] {
            font-size: 8px;
        }
        .text-[9px] {
            font-size: 9px;
        }
        .text-[10px] {
            font-size: 10px;
        }
        .text-[11px] {
            font-size: 11px;
        }
        .text-[12px] {
            font-size: 12px;
        }
        
        /* Minimalist Flat Active State with iPhone Curves */
        .nav-item-active {
            background-color: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            font-weight: 700;
            border-radius: 16px;
        }
        .dark .nav-item-active { 
            background-color: rgba(37, 99, 235, 0.15); 
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 flex flex-col md:flex-row h-screen overflow-hidden min-h-screen">

    <!-- Global Loading Overlay -->
    <div id="globalLoader" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex-col items-center justify-center">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-slate-200 dark:border-slate-700 rounded-full"></div>
            <div class="absolute top-0 left-0 w-16 h-16 border-4 border-blue-600 rounded-full border-t-transparent animate-spin" style="animation-duration: 0.6s;"></div>
        </div>
        <p class="mt-4 text-white text-[10px] font-bold uppercase tracking-widest">Memuat...</p>
    </div>

    <script>
        // Global loading functions
        function showLoading() {
            const loader = document.getElementById('globalLoader');
            if (loader) {
                loader.classList.remove('hidden');
                loader.classList.add('flex');
            }
        }
        function hideLoading() {
            const loader = document.getElementById('globalLoader');
            if (loader) {
                loader.classList.add('hidden');
                loader.classList.remove('flex');
            }
        }
        
        // Auto hide after timeout (5 detik max)
        let loadingTimeout;
        function startLoadingTimer() {
            clearTimeout(loadingTimeout);
            loadingTimeout = setTimeout(hideLoading, 5000);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Hanya form submit yang menuju ke server (method POST/PUT/DELETE)
            document.addEventListener('submit', function(e) {
                const target = e.target;
                if (target.tagName === 'FORM' && !target.classList.contains('no-loading')) {
                    const method = target.method?.toUpperCase();
                    if (method === 'POST' || method === 'PUT' || method === 'PATCH' || method === 'DELETE') {
                        showLoading();
                        startLoadingTimer();
                    }
                }
            }, true);
            
            // 2. Hanya link navigasi ke halaman lain (bukan anchor/javascript)
            document.addEventListener('click', function(e) {
                const target = e.target.closest('a');
                if (target) {
                    const href = target.getAttribute('href');
                    const onclick = target.getAttribute('@click');
                    // Hanya link yang menuju halaman baru (bukan #, javascript, atau Alpine)
                    if (href && !href.startsWith('#') && !href.startsWith('javascript') && !href.startsWith('mailto:') && !href.startsWith('tel:') && !onclick && !target.classList.contains('no-loading')) {
                        showLoading();
                        startLoadingTimer();
                    }
                }
            }, true);
            
            // 3. Hide loading on page load complete
            window.addEventListener('load', function() {
                hideLoading();
                clearTimeout(loadingTimeout);
            });
            
            // 4. Hide on visibility change (tab switch)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    hideLoading();
                    clearTimeout(loadingTimeout);
                }
            });
        });
    </script>

    <div x-show="mobileSidebar" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebar = false" 
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-40 lg:hidden" x-cloak></div>

    <aside 
        class="sidebar-transition shrink-0 h-screen bg-transparent z-50 overflow-hidden flex flex-col justify-between
               fixed lg:relative inset-y-0 left-0 lg:translate-x-0"
        :class="[
            sidebarCollapsed ? 'lg:w-14' : 'lg:w-[13rem]',
            mobileSidebar ? 'translate-x-0 w-52 bg-slate-50 dark:bg-slate-900' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <div class="flex flex-col h-full px-2">
            <div class="h-12 flex items-center shrink-0 justify-between lg:justify-start px-2"
                 :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                    <div class="w-6 h-6 shrink-0">
                        <img src="{{ asset('logo/logo_original.png') }}" class="w-full h-full object-contain dark:hidden" alt="TaxFlow Logo">
                        <img src="{{ asset('logo/logo_white.png') }}" class="w-full h-full object-contain hidden dark:block" alt="TaxFlow Logo White">
                    </div>
                    <h1 class="text-[12px] font-black text-slate-800 dark:text-white tracking-tighter" x-show="!sidebarCollapsed || mobileSidebar">Tax<span class="text-blue-600">Flow</span></h1>
                </a>
                <button @click="mobileSidebar = false" class="lg:hidden text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="ph ph-x text-lg"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto pt-4 space-y-0.5">
                <div class="px-3 mb-2" x-show="!sidebarCollapsed || mobileSidebar">
                    <p class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Konsol</p>
                </div>

                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('dashboard') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-squares-four text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Dasbor</span>
                    </a>
                    <a href="{{ route('pajak.index') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('pajak.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-files text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Basis Pajak</span>
                    </a>
                    <a href="{{ route('monitoring.index') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('monitoring.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-chart-line-up text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Pemantauan</span>
                    </a>
                    <a href="{{ route('dokumen.index') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('dokumen.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-folder-open text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Dokumen</span>
                    </a>

                    <div class="px-3 my-3 pt-3" x-show="!sidebarCollapsed || mobileSidebar">
                        <p class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none">Sistem</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('user.index') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('user.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-users-three text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Kelola Akun</span>
                    </a>
                    <a href="{{ route('approval.dashboard') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('approval.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-check-circle text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Approval</span>
                    </a>
                    @endif
                    <a href="{{ route('laporan.index') }}" wire:navigate class="flex items-center gap-2.5 py-2 transition-all hover:bg-slate-200/40 dark:hover:bg-slate-800/40 rounded-2xl {{ request()->routeIs('laporan.*') ? 'nav-item-active' : 'text-slate-500 dark:text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-chart-pie text-lg"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Laporan</span>
                    </a>
                </div>
            </nav>

            <div class="pb-4 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 py-2 hover:bg-rose-500/10 text-slate-500 hover:text-rose-600 rounded-2xl transition-all group"
                            :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0 mx-1' : 'px-3'">
                        <i class="ph ph-power text-lg group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-semibold" x-show="!sidebarCollapsed || mobileSidebar">Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="h-12 bg-transparent flex items-center justify-between px-4 lg:px-6 shrink-0 z-30">
            <div class="flex items-center gap-3">
                <button @click="toggleSidebar()" class="w-7 h-7 rounded-[10px] bg-slate-200/50 dark:bg-slate-800/50 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-slate-200 dark:hover:bg-slate-800 transition-all">
                    <i class="ph ph-list text-lg"></i>
                </button>
                <h2 class="hidden md:block text-[11px] font-bold text-slate-800 dark:text-white tracking-widest uppercase truncate lg:max-w-none">@yield('page-title', 'Ringkasan Utama')</h2>
                
                <div class="flex items-center gap-1 md:gap-2 ml-1 md:ml-2 pl-1 md:pl-2">
                    @yield('actions')
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button @click="toggleTheme()" class="w-7 h-7 rounded-[10px] bg-slate-200/50 dark:bg-slate-800/50 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all group relative overflow-hidden">
                    <i class="ph ph-sun text-lg transition-all duration-500" :class="isDark ? 'translate-y-8 opacity-0' : 'translate-y-0 opacity-100'"></i>
                    <i class="ph ph-moon-stars text-lg absolute transition-all duration-500" :class="isDark ? 'translate-y-0 opacity-100' : '-translate-y-8 opacity-0'"></i>
                </button>

                <div class="relative" x-data="notificationSystem()" x-init="initNotifications()">
                    <button @click="open = !open" class="w-7 h-7 rounded-[10px] bg-slate-200/50 dark:bg-slate-800/50 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all relative">
                        <i class="ph ph-bell text-lg"></i>
                        <template x-if="count > 0">
                            <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 text-transparent text-[0px] rounded-full" x-text="count"></span>
                        </template>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-3 w-64 lg:w-72 bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-[2rem] overflow-hidden z-50">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                            <h4 class="text-[9px] font-bold text-slate-800 dark:text-white uppercase tracking-widest leading-none">Notifikasi</h4>
                            <span class="bg-blue-600 text-white px-2 py-0.5 rounded-full text-[8px] font-bold" x-text="count + ' BARU'"></span>
                        </div>
                        <div class="max-h-[20rem] overflow-y-auto">
                            <template x-for="item in items" :key="item.id">
                                <a :href="item.url" wire:navigate class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-900 border-b border-slate-100/50 dark:border-slate-700/30 last:border-0 border-l-[3px]" :class="'border-l-' + (item.type === 'danger' ? 'rose-500' : (item.type === 'warning' ? 'amber-500' : 'blue-500'))">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="text-[9px] font-bold text-slate-800 dark:text-white uppercase truncate" x-text="item.title"></p>
                                        <span class="text-[8px] text-slate-400 shrink-0" x-text="item.time_human"></span>
                                    </div>
                                    <p class="text-[9px] font-medium leading-tight" :class="'text-' + (item.type === 'danger' ? 'rose-600' : (item.type === 'warning' ? 'amber-600' : 'blue-600'))" x-text="item.message"></p>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ openProfile: false }">
                    <button @click="openProfile = !openProfile" class="w-7 h-7 lg:w-8 lg:h-8 rounded-[12px] bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md text-white flex items-center justify-center font-bold text-[10px] uppercase ml-1 focus:outline-none">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </button>

                    <div x-show="openProfile" x-cloak @click.away="openProfile = false" 
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-48 bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/50 flex flex-col">
                            <span class="text-[10px] font-bold text-slate-800 dark:text-white uppercase tracking-widest leading-none truncate">{{ auth()->user()->name }}</span>
                            <span class="text-[8px] text-slate-500 mt-1 truncate">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-500 rounded-xl transition-all group">
                                    <i class="ph ph-power text-sm group-hover:scale-110 transition-transform"></i>
                                    <span class="text-[9px] font-black uppercase tracking-tight">Keluar Sesi</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 pb-6 md:px-8 md:pb-8 transition-colors">
            <div class="w-full h-full mx-auto" x-init="$el.classList.add('page-fade-enter-active')" id="main-content">
                
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/20 rounded-[1.5rem] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <i class="ph ph-check-circle text-base"></i>
                        </div>
                        <p class="text-[10px] font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-500/50 hover:text-emerald-600 transition-colors mr-1">
                        <i class="ph ph-x-circle text-lg"></i>
                    </button>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div x-data="{ show: true }" x-show="show" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="mb-4 p-4 bg-rose-50 dark:bg-rose-500/10 border border-rose-200/50 dark:border-rose-500/20 rounded-[1.5rem]">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0">
                                <i class="ph ph-warning-circle text-base"></i>
                            </div>
                            <p class="text-[10px] font-bold text-rose-800 dark:text-rose-400 uppercase tracking-wide">Sistem Mendeteksi Galat</p>
                        </div>
                        <button @click="show = false" class="text-rose-500/50 hover:text-rose-600 transition-colors">
                            <i class="ph ph-x-circle text-lg"></i>
                        </button>
                    </div>
                    @if(session('error'))
                        <p class="text-[9px] font-medium text-rose-600 dark:text-rose-400 ml-9">{{ session('error') }}</p>
                    @endif
                    @if($errors->any())
                        <ul class="ml-9 mt-1 space-y-0.5 list-disc list-inside text-rose-600/80 dark:text-rose-400/80 text-[9px] uppercase tracking-tight">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @endif

                @yield('content')

            </div>
        </main>

    </div>

    <div x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
        <div x-show="showDeleteModal" @click="showDeleteModal = false"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>

        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white dark:bg-slate-800 w-full max-w-xs rounded-[2.5rem] border border-slate-200/50 dark:border-slate-700/50 overflow-hidden text-center p-6">
            
            <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-[1.5rem] flex items-center justify-center mx-auto mb-4 text-rose-500">
                <i class="ph ph-trash text-2xl"></i>
            </div>
            <h3 class="text-[11px] font-bold text-slate-800 dark:text-white uppercase tracking-widest mb-1">Hapus Permanen</h3>
            <p class="text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                Data <span class="text-slate-800 dark:text-slate-200 font-bold" x-text="deleteTitle"></span> akan dihapus dari sistem.
            </p>
            <div class="flex flex-col gap-2">
                <button @click="executeDelete()" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-[9px] font-bold uppercase tracking-widest transition-all">Hapus Sekarang</button>
                <button @click="showDeleteModal = false" class="w-full py-3 bg-slate-100/50 dark:bg-slate-700/30 text-slate-500 dark:text-slate-400 rounded-2xl text-[9px] font-bold uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Batalkan</button>
            </div>
        </div>
    </div>

    @livewireScripts
    <script>
        function notificationSystem() {
            return {
                open: false, count: 0, items: [],
                initNotifications() { this.fetchNotifications(); setInterval(() => this.fetchNotifications(), 45000); },
                async fetchNotifications() {
                    try {
                        const res = await fetch('{{ route('api.notifications') }}');
                        const data = await res.json();
                        this.count = data.count; this.items = data.items;
                    } catch (e) {}
                }
            }
        }
        document.addEventListener('livewire:navigating', () => {
            const content = document.getElementById('main-content');
            if (content) content.classList.remove('page-fade-enter-active');
        });
        document.addEventListener('livewire:navigated', () => {
            const content = document.getElementById('main-content');
            if (content) {
                content.classList.add('page-fade-enter');
                setTimeout(() => content.classList.add('page-fade-enter-active'), 50);
                window.scrollTo({ top: 0, behavior: 'instant' });
            }
        });
    </script>
</body>
</html> 