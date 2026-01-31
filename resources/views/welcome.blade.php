<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .text-gradient {
            background: linear-gradient(to right, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-300 h-screen w-full overflow-hidden flex flex-col selection:bg-blue-500 selection:text-white">
    
    <!-- Background Gradients -->
    <div class="fixed inset-0 -z-10 h-full w-full pointer-events-none">
        <div class="absolute top-0 left-1/4 w-[600px] h-[500px] bg-blue-600/20 blur-[130px] rounded-full mix-blend-screen opacity-60"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[500px] bg-purple-600/10 blur-[130px] rounded-full mix-blend-screen opacity-50"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-100"></div>
    </div>

    <!-- Navigation -->
    <nav class="glass-nav absolute w-full z-50 top-0 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 ring-1 ring-white/20">
                         <!-- Logo Icon -->
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="font-bold text-xl text-white tracking-tight">{{ config('app.name', 'Rekening') }}</span>
                </div>
                
                <div>
                     @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/agent/dashboard') }}" class="px-5 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-sm font-medium transition-all ring-1 ring-white/10 hover:ring-white/30">Dashboard</a>
                            @else
                                <a href="{{ url('/agent/login') }}" class="text-sm font-medium hover:text-white transition-colors">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-medium shadow-lg shadow-blue-500/25 transition-all transform hover:scale-105 active:scale-95">Get Started</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center relative w-full px-4">
        <!-- Neon Wrapper -->
        <div class="relative p-[3px] rounded-3xl overflow-hidden group">
            
            <!-- Moving Neon Gradient (Sorop Neon Berjalan) -->
            <div class="absolute inset-0 bg-[conic-gradient(from_0deg,transparent_0_300deg,#60a5fa_330deg,#a855f7_360deg)] animate-[spin_4s_linear_infinite]"></div>
            
            <!-- Inner Content -->
            <div class="relative bg-[#020617] rounded-[22px] px-8 py-12 md:px-16 md:py-20 text-center z-10">
                <!-- Background glow behind text for extra pop -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-blue-500/20 blur-[60px] rounded-full pointer-events-none"></div>

                <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight text-white leading-tight">
                    Manage your wealth<br>
                    <span class="text-gradient">Integrity & Speed</span>
                </h1>
            </div>
        </div>
    </main>

</body>
</html>
