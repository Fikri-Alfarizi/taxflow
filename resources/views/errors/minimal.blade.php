<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - TaxFlow Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        if (localStorage.getItem('isDark') === 'true' || (!('isDark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 antialiased h-screen w-screen flex items-center justify-center p-4 overflow-hidden relative">

    <div class="max-w-lg w-full text-center relative z-10 animate-[translate-y_0.5s_ease-out]">
        
        <!-- Big Number Error Code -->
        <div class="text-[7rem] md:text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-blue-600 to-indigo-800 dark:from-blue-400 dark:to-indigo-500 mb-2 select-none relative inline-block drop-shadow-sm">
            @yield('code')
            
            <div class="absolute -top-2 md:-top-6 -right-6 md:-right-10 text-slate-200 dark:text-slate-800/80 text-6xl md:text-8xl -z-10 md:rotate-12">
                @yield('icon', '<i class="ph ph-warning-circle"></i>')
            </div>
        </div>

        <!-- Title & Message -->
        <div class="bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700/60 px-8 py-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-none mb-8 mt-6 relative transform transition-transform duration-300 hover:scale-[1.01]">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center mx-auto absolute -top-6 left-1/2 -translate-x-1/2 border-4 border-white dark:border-slate-800 border-solid">
                <i class="ph ph-shield-warning text-2xl font-bold"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-black uppercase tracking-widest mb-3 mt-2 text-slate-800 dark:text-white">@yield('title')</h1>
            <p class="text-xs md:text-sm font-semibold text-slate-500 dark:text-slate-400 leading-relaxed">@yield('message')</p>
        </div>

        <!-- Actions -->
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-slate-900 border border-transparent dark:bg-white text-white dark:text-slate-900 font-bold text-xs uppercase tracking-widest rounded-2xl flex-shrink-0 transition-transform active:scale-95 shadow-lg shadow-slate-300 dark:shadow-none hover:bg-slate-800 hover:border-slate-700 dark:hover:bg-slate-200">
                <i class="ph ph-house text-lg"></i> KEMBALI KE BERANDA
            </a>
            
            <div class="mt-12 select-none">
                <img src="{{ asset('logo/logo_original.png') }}" alt="TaxFlow" class="h-6 mx-auto dark:hidden opacity-30 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                <img src="{{ asset('logo/logo_white.png') }}" alt="TaxFlow" class="h-6 mx-auto hidden dark:block opacity-30 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-300">
            </div>
        </div>

    </div>

    <!-- Background Decoration -->
    <div class="fixed inset-0 pointer-events-none -z-10 flex items-center justify-center">
        <div class="w-[600px] h-[600px] bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-[100px] absolute translate-x-1/2 -translate-y-1/4 animate-pulse duration-[10s]"></div>
        <div class="w-[500px] h-[500px] bg-rose-500/5 dark:bg-rose-500/10 rounded-full blur-[100px] absolute -translate-x-1/2 translate-y-1/4 animate-pulse duration-[7s]"></div>
    </div>

</body>
</html>
