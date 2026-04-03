<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaxFlow Professional</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <!-- Brand -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-xl shadow-blue-200 mb-4">
                <i class="ph ph-shield-check text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Tax<span class="text-blue-600">Flow</span></h1>
            <p class="text-slate-400 text-sm font-medium mt-2">Enterprise Tax Monitoring System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <div class="p-8 md:p-10">
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-slate-800">Selamat Datang</h2>
                    <p class="text-slate-400 text-xs mt-1 font-medium italic">Silakan login untuk mengakses dashboard internal.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-xs font-bold animate-shake">
                        <i class="ph ph-warning-circle text-lg"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Email Karyawan</label>
                        <div class="relative group">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="email" name="email" id="email" required 
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all" 
                                placeholder="nama@taxflow.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                             <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Password Akses</label>
                             <a href="#" class="text-[10px] font-bold text-blue-600 hover:underline uppercase tracking-tight">Lupa Password?</a>
                        </div>
                        <div class="relative group">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="password" name="password" id="password" required 
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-100 focus:bg-white transition-all" 
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs text-slate-500 font-medium group-hover:text-slate-700 transition-colors">Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-xl shadow-slate-200 transition-all text-xs tracking-widest uppercase flex items-center justify-center gap-2">
                        Masuk Ke Sistem <i class="ph ph-arrow-right font-bold text-lg"></i>
                    </button>
                </form>
            </div>
            
            <div class="bg-slate-50 py-4 px-8 border-t border-slate-100 text-center">
                <p class="text-[10px] text-slate-400 font-medium">© 2026 TaxFlow Professional. All rights reserved.</p>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-[10px] text-slate-300 font-bold uppercase tracking-[0.2em] italic">Internal Access Only</p>
        </div>
    </div>

</body>
</html>
