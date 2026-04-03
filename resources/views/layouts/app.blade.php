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
    
    <!-- Comprehensive Favicon Branding -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('logo/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('logo/favicon/favicon.ico') }}">
    
    <!-- Zero-Latency Theme Ingestion -->
    <script>
        if (localStorage.getItem('isDark') === 'true' || (!('isDark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS with Dark Mode Config -->
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
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    @livewireStyles
    @yield('head')
    
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 13px; }
        [x-cloak] { display: none !important; }
        * {
            -ms-overflow-style: none;
            scrollbar-width: none;
            transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        border-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        color 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        *::-webkit-scrollbar { display: none; }
        .sidebar-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .nav-item-active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.06) 0%, rgba(37, 99, 235, 0) 100%);
            border-left: 2.5px solid #2563eb;
            color: #2563eb;
            font-weight: 800;
        }
        .dark .nav-item-active { background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0) 100%); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 flex flex-col md:flex-row h-screen overflow-hidden">

    <!-- Mobile Drawer Overlay -->
    <div x-show="mobileSidebar" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebar = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" x-cloak></div>

    <!-- Sidebar (Desktop & Mobile Drawer) -->
    <aside 
        class="sidebar-transition shrink-0 h-screen bg-white dark:bg-[#020617] border-r border-slate-100 dark:border-slate-800 z-50 overflow-hidden flex flex-col justify-between
               fixed lg:relative inset-y-0 left-0 lg:translate-x-0"
        :class="[
            sidebarCollapsed ? 'lg:w-16' : 'lg:w-60',
            mobileSidebar ? 'translate-x-0 w-64 shadow-2xl' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <div class="flex flex-col h-full">
            <div class="h-14 flex items-center border-b border-slate-100 dark:border-slate-800 shrink-0 px-6 justify-between lg:justify-start"
                 :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'lg:px-6'">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                    <div class="w-8 h-8 shrink-0">
                        <img src="{{ asset('logo/logo_original.png') }}" class="w-full h-full object-contain dark:hidden" alt="TaxFlow Logo">
                        <img src="{{ asset('logo/logo_white.png') }}" class="w-full h-full object-contain hidden dark:block" alt="TaxFlow Logo White">
                    </div>
                    <h1 class="text-base font-black text-slate-800 dark:text-white tracking-tighter" x-show="!sidebarCollapsed || mobileSidebar">Tax<span class="text-blue-600">Flow</span></h1>
                </a>
                <button @click="mobileSidebar = false" class="lg:hidden text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto pt-6 space-y-0.5">
                <div class="px-6 mb-3" x-show="!sidebarCollapsed || mobileSidebar">
                    <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest leading-none">Konsol Utama</p>
                </div>

                <div class="space-y-0.5">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('dashboard') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-squares-four text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Dasbor Utama</span>
                    </a>
                    <a href="{{ route('pajak.index') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('pajak.*') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-files text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Basis Data Pajak</span>
                    </a>
                    <a href="{{ route('monitoring.index') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('monitoring.*') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-chart-line-up text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Log Pemantauan</span>
                    </a>
                    <a href="{{ route('dokumen.index') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('dokumen.*') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-folder-open text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Pusat Dokumen</span>
                    </a>

                    <div class="px-6 my-4 border-t border-slate-50 dark:border-slate-800 pt-4" x-show="!sidebarCollapsed || mobileSidebar">
                        <p class="text-[9px] font-black text-slate-300 dark:text-slate-500 uppercase tracking-widest leading-none">Administrasi & Laporan</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('user.index') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('user.*') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-users-three text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Kelola Akun</span>
                    </a>
                    @endif
                    <a href="{{ route('laporan.index') }}" wire:navigate class="flex items-center gap-3 py-2.5 transition-all hover:bg-slate-100/50 dark:hover:bg-slate-800/20 {{ request()->routeIs('laporan.*') ? 'nav-item-active' : 'text-slate-400' }}" :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-6'">
                        <i class="ph ph-chart-pie text-xl"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Arsip Laporan</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-100/50 dark:border-slate-800/50 bg-slate-50/30 dark:bg-slate-900/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 py-2 px-3 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-slate-400 hover:text-rose-600 rounded-xl transition-all group"
                            :class="sidebarCollapsed && !mobileSidebar ? 'justify-center px-0' : 'px-3'">
                        <i class="ph ph-power text-xl group-hover:rotate-12 transition-transform"></i>
                        <span class="text-[12px] font-bold" x-show="!sidebarCollapsed || mobileSidebar">Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-14 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 shrink-0 z-30">
            <div class="flex items-center gap-3 lg:gap-4">
                <button @click="toggleSidebar()" class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-white dark:hover:bg-slate-700 transition-all shadow-sm">
                    <i class="ph ph-list text-xl"></i>
                </button>
                <h2 class="text-[12px] md:text-sm font-black text-slate-800 dark:text-white tracking-widest uppercase truncate max-w-[150px] md:max-w-none">@yield('page-title', 'Ringkasan Utama')</h2>
                
                <!-- Global Actions Yield -->
                <div class="flex items-center gap-1 md:gap-2 border-l border-slate-100 dark:border-slate-800 ml-2 md:ml-4 pl-2 md:pl-4">
                    @yield('actions')
                </div>
            </div>

            <div class="flex items-center gap-2 lg:gap-3">
                <!-- Theme Toggle -->
                <button @click="toggleTheme()" class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all group relative overflow-hidden">
                    <i class="ph ph-sun text-xl transition-all duration-500" :class="isDark ? 'translate-y-8 opacity-0' : 'translate-y-0 opacity-100'"></i>
                    <i class="ph ph-moon-stars text-xl absolute transition-all duration-500" :class="isDark ? 'translate-y-0 opacity-100' : '-translate-y-8 opacity-0'"></i>
                </button>

                <!-- Notifications (Simplified on Mobile) -->
                <div class="relative" x-data="notificationSystem()" x-init="initNotifications()">
                    <button @click="open = !open" class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-all relative">
                        <i class="ph ph-bell text-xl"></i>
                        <template x-if="count > 0">
                            <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 text-white text-[8px] font-black flex items-center justify-center rounded-full border-2 border-white dark:border-slate-900" x-text="count"></span>
                        </template>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 mt-3 w-72 lg:w-80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden z-50">
                        <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 flex justify-between items-center">
                            <h4 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-widest leading-none">Notifikasi Sistem</h4>
                            <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-[8px] font-black" x-text="count + ' BARU'"></span>
                        </div>
                        <div class="max-h-[25rem] overflow-y-auto">
                            <template x-for="item in items" :key="item.id">
                                <a :href="item.url" wire:navigate class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-900 border-b border-slate-50 dark:border-slate-700 last:border-0 border-l-2" :class="'border-l-' + (item.type === 'danger' ? 'rose-500' : (item.type === 'warning' ? 'amber-500' : 'blue-500'))">
                                    <div class="flex justify-between items-start">
                                        <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase truncate" x-text="item.title"></p>
                                        <span class="text-[8px] font-bold text-slate-300 dark:text-slate-500 shrink-0" x-text="item.time_human"></span>
                                    </div>
                                    <p class="text-[9px] font-black italic mt-1 leading-tight" :class="'text-' + (item.type === 'danger' ? 'rose-600' : (item.type === 'warning' ? 'amber-600' : 'blue-600'))" x-text="item.message"></p>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="w-8 h-8 lg:w-9 lg:h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-[10px] uppercase shadow-lg shadow-blue-100">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 py-6 md:p-10 bg-slate-50 dark:bg-slate-950 transition-colors">
            <div class="max-w-[1400px] mx-auto" x-init="$el.classList.add('page-fade-enter-active')" id="main-content">
                
                <!-- Premium Notification System -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-800/50 rounded-2xl flex items-center justify-between shadow-sm shadow-emerald-100 dark:shadow-none">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-200 dark:shadow-none">
                            <i class="ph ph-check-circle text-lg"></i>
                        </div>
                        <p class="text-[11px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-300 hover:text-emerald-500 transition-colors">
                        <i class="ph ph-x-circle text-xl"></i>
                    </button>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div x-data="{ show: true }" x-show="show" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-800/50 rounded-2xl shadow-sm shadow-rose-100 dark:shadow-none">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-rose-200 dark:shadow-none">
                                <i class="ph ph-warning-circle text-lg"></i>
                            </div>
                            <p class="text-[11px] font-black text-rose-800 dark:text-rose-400 uppercase tracking-widest">Sistem Mendeteksi Galat</p>
                        </div>
                        <button @click="show = false" class="text-rose-300 hover:text-rose-500 transition-colors">
                            <i class="ph ph-x-circle text-xl"></i>
                        </button>
                    </div>
                    @if(session('error'))
                        <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 ml-11 italic">{{ session('error') }}</p>
                    @endif
                    @if($errors->any())
                        <ul class="ml-11 mt-1 space-y-1 list-disc list-inside text-rose-600/80 dark:text-rose-400/80 text-[9px] font-bold uppercase tracking-tight">
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

    <!-- Premium Global Delete Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-6" x-cloak>
        <!-- Backdrop -->
        <div x-show="showDeleteModal" @click="showDeleteModal = false"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/40 dark:bg-slate-950/80 backdrop-blur-sm"></div>

        <!-- Modal Card -->
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative bg-white dark:bg-slate-800 w-full max-w-sm rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
            
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 dark:bg-rose-950/30 rounded-full flex items-center justify-center mx-auto mb-6 border border-rose-100 dark:border-rose-900">
                    <i class="ph ph-trash text-3xl text-rose-600 dark:text-rose-500 animate-pulse"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest mb-2">Konfirmasi Hapus Permanen</h3>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 px-4 leading-relaxed mb-8 italic">
                    Tindakan ini tidak dapat dibatalkan. Sistem akan menghapus data <span class="text-slate-800 dark:text-slate-200 font-black" x-text="deleteTitle"></span> dari database terpusat.
                </p>
                <div class="flex flex-col gap-3">
                    <button @click="executeDelete()" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-rose-100 dark:shadow-none">HAPUS SEKARANG PERMANEN</button>
                    <button @click="showDeleteModal = false" class="w-full py-3 bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border border-slate-100 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 transition-all">BATALKAN PROSES</button>
                </div>
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
