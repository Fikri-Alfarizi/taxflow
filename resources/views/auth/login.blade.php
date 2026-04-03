<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TaxFlow</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Comprehensive Favicon Branding -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('logo/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('logo/favicon/favicon.ico') }}">
    
    <style>
        body { font-family: 'Inter', sans-serif; overflow: hidden; }
        
        .bg-mesh {
            background-color: #2e10db;
            background-image: 
                radial-gradient(at 0% 0%, #ffffff 0px, transparent 65%),
                radial-gradient(at 100% 0%, #dca1ff 0px, transparent 65%),
                radial-gradient(at 100% 100%, #d8f3ff 0px, transparent 60%),
                radial-gradient(at 0% 100%, #100566 0px, transparent 70%);
        }

        /* Responsive height adjustments */
        @media (max-height: 800px) {
            .login-container { transform: scale(0.9); }
            body { overflow: auto; } /* Failsafe for very small screens */
        }
    </style>
</head>
<body class="bg-white h-screen flex items-center justify-center p-4 lg:p-8 overflow-hidden">

    <div class="login-container w-full max-w-[1200px] h-full max-h-[800px] bg-white flex flex-col lg:flex-row gap-8 lg:gap-16 items-stretch">
        
        <!-- Left Visual Panel (Responsive Height) -->
        <div class="lg:w-[45%] w-full h-[300px] lg:h-auto bg-mesh rounded-[2rem] p-10 flex flex-col justify-between items-start shrink-0">
            <div class="w-12 h-12">
                <img src="{{ asset('logo/logo_white.png') }}" class="w-full h-full object-contain" alt="White Logo">
            </div>

            <div class="space-y-3 pr-4">
                <p class="text-white/90 text-sm font-medium tracking-wide">Kelola Pajak Anda</p>
                <h2 class="text-white text-[2.5rem] lg:text-[2.75rem] font-bold leading-[1.1] tracking-tight">
                    Akses pusat pantauan pajak Anda dengan mudah & akurat
                </h2>
            </div>
        </div>

        <!-- Right Form Panel (Compact & Functional) -->
        <div class="lg:w-[55%] w-full flex flex-col justify-center px-4 lg:px-12 py-2 relative h-full">
            <div class="w-full max-w-[420px] mx-auto lg:mx-0 space-y-6">
                
                <div class="text-left space-y-3">
                     <div class="w-12 h-12 mb-2">
                        <img src="{{ asset('logo/logo_original.png') }}" class="w-full h-full object-contain" alt="Logo Original">
                     </div>
                     <h1 class="text-[32px] font-bold text-black tracking-tight leading-none">Masuk ke Akun</h1>
                     <p class="text-gray-500 text-[13px] leading-relaxed pr-8">
                        Pantau laporan, kelola dokumen, dan pastikan kewajiban perpajakan Anda terpenuhi tepat waktu.
                     </p>
                </div>

                @if ($errors->any())
                    <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest animate-pulse">
                        <i class="ph ph-warning-circle text-lg"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                                        
                    <div class="space-y-1.5">
                        <label for="email" class="block text-[13px] font-bold text-gray-900">Email Anda</label>
                        <input type="email" name="email" id="email" required 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#4f39f6] outline-none transition-colors placeholder:text-gray-500 shadow-sm" 
                            placeholder="email@perusahaan.com" value="{{ old('email') }}">
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-[13px] font-bold text-gray-900">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#4f39f6] outline-none transition-colors tracking-widest placeholder:text-gray-400 placeholder:tracking-widest shadow-sm" 
                                placeholder="••••••••••••">
                            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="ph ph-eye text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-2 py-3.5 bg-[#4f39f6] hover:bg-[#432ddb] text-white font-medium rounded-lg shadow-[0_8px_20px_-6px_rgba(79,57,246,0.6)] active:scale-[0.99] transition-all text-[15px] h-[52px] flex items-center justify-center">
                        Masuk Sekarang
                    </button>
                </form>
            </div>
        </div>

    </div>

</body>
</html>