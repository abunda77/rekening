<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Command Center') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#050202] text-white font-mono antialiased overflow-hidden selection:bg-red-500 selection:text-white">

    <div class="fixed inset-0 min-h-screen flex items-center justify-center relative overflow-hidden">
        
        <!-- CRT/Scanline Effect Overlay -->
        <div class="pointer-events-none fixed inset-0 z-50 mix-blend-overlay opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
        <div class="pointer-events-none fixed inset-0 z-40 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"></div>

        <!-- Background Map/Grid -->
        <div class="absolute inset-0 z-0 opacity-20">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#3f1010_1px,transparent_1px),linear-gradient(to_bottom,#3f1010_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-[#050202] via-transparent to-transparent"></div>
        </div>

        <!-- Main Container -->
        <div class="relative z-20 w-full max-w-lg p-4">
            
            <!-- Header / Warning -->
            <div class="text-center mb-8 space-y-2">
                <div class="inline-block border border-amber-500/50 bg-amber-500/10 px-3 py-1 text-xs text-amber-500 tracking-[0.3em] uppercase animate-pulse">
                    ⚠ Top Secret clearance required
                </div>
            </div>

            <!-- Login Card -->
            <div class="bg-red-950/10 backdrop-blur-md border border-red-900/50 shadow-2xl relative overflow-hidden group">
                
                <!-- Corner Accents -->
                <div class="absolute top-0 left-0 w-4 h-4 border-l-2 border-t-2 border-red-600"></div>
                <div class="absolute top-0 right-0 w-4 h-4 border-r-2 border-t-2 border-red-600"></div>
                <div class="absolute bottom-0 left-0 w-4 h-4 border-l-2 border-b-2 border-red-600"></div>
                <div class="absolute bottom-0 right-0 w-4 h-4 border-r-2 border-b-2 border-red-600"></div>

                <div class="p-4 md:p-4 relative">
                    
                    <!-- Logo Section -->
                    <div class="flex flex-col items-center mb-10">
                        <div class="w-20 h-20 mb-6 relative">
                             <!-- Custom Hexagon Logo -->
                            <svg class="w-full h-full text-red-600 drop-shadow-[0_0_15px_rgba(220,38,38,0.5)]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 5L93.3 25V75L50 95L6.7 75V25L50 5Z" stroke="currentColor" stroke-width="2" fill="none"/> <!-- Hexagon -->
                                <circle cx="50" cy="50" r="20" stroke="currentColor" stroke-width="1.5" class="animate-pulse opacity-80"/>
                                <path d="M50 30V70M30 50H70" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <rect x="45" y="45" width="10" height="10" fill="currentColor" class="animate-ping"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl text-white font-black tracking-widest uppercase font-mono">Command Center</h1>
                        <div class="text-xs text-red-500 mt-2 tracking-[0.2em] uppercase">Root Access Terminal-X</div>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Email Input -->
                        <div class="space-y-1">
                            <label class="text-xs text-red-500 uppercase tracking-widest pl-1">Administrator ID</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-700 group-focus-within:text-red-500">
                                    >
                                </div>
                                <input type="email" 
                                       name="email"
                                       value="{{ old('email') }}"
                                       required 
                                       autofocus
                                       autocomplete="email"
                                       class="block w-full bg-black/50 border border-red-900/50 text-red-100 py-3 pl-8 pr-4 placeholder-red-900/50 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:bg-red-950/30 transition-all font-mono tracking-widest"
                                       placeholder="ROOT@SYSTEM">
                            </div>
                             @error('email') 
                                <div class="text-red-500 text-xs mt-1 font-bold tracking-wide animate-pulse">
                                    [ERROR]: {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="space-y-1">
                            <label class="text-xs text-red-500 uppercase tracking-widest pl-1">Security Token</label>
                            <div class="relative group">
                                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-700 group-focus-within:text-red-500">
                                    #
                                </div>
                                <input type="password" 
                                       name="password"
                                       required 
                                       autocomplete="current-password"
                                       class="block w-full bg-black/50 border border-red-900/50 text-red-100 py-3 pl-8 pr-4 placeholder-red-900/50 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:bg-red-950/30 transition-all font-mono tracking-widest"
                                       placeholder="••••••••">
                            </div>
                            @error('password') 
                                <div class="text-red-500 text-xs mt-1 font-bold tracking-wide animate-pulse">
                                    [ERROR]: {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-red-600 border-red-900 bg-black rounded focus:ring-red-500 focus:ring-offset-black">
                                <span class="text-xs text-red-400/70 uppercase tracking-wider">Keep Session Alive</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-red-500/60 hover:text-red-400 uppercase tracking-wider transition-colors">[ Reset Protocol ]</a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="w-full relative group overflow-hidden mt-6 h-14 bg-black border border-red-600/50 hover:border-red-500 transition-all duration-300">
                            <div class="absolute inset-0 bg-red-900/20 group-hover:bg-red-600/20 transition-colors"></div>
                            
                            <!-- Static Content -->
                            <div id="btn-static" class="flex items-center justify-center h-full relative z-10">
                                <span class="text-red-500 font-bold tracking-[0.2em] text-sm uppercase group-hover:text-red-400 transition-colors flex items-center gap-2">
                                    <span class="animate-pulse">></span> AUTHENTICATE <span class="animate-pulse"><</span>
                                </span>
                            </div>

                            <!-- Loading/Typing Content (Hidden by default) -->
                            <div id="btn-loading" class="hidden absolute inset-0 flex items-center justify-center h-full z-20 bg-black">
                                <div class="flex items-center">
                                    <span class="text-green-500 font-mono text-xs md:text-sm tracking-widest uppercase typing-effect">
                                        >> BYPASSING SECURITY...
                                    </span>
                                </div>
                            </div>

                            <!-- Scanline on Hover -->
                            <div class="absolute top-0 left-0 w-full h-[2px] bg-red-500/50 -translate-y-full group-hover:translate-y-[60px] transition-transform duration-1000 ease-linear"></div>
                        </button>
                    </form>

                    <div class="mt-4 text-center space-y-2">
                         @if (Route::has('register'))
                            <div class="text-[10px] uppercase text-red-500/50 tracking-widest">
                                New Personnel? <a href="{{ route('register') }}" class="text-red-400 hover:text-red-300 underline underline-offset-4 decoration-1 decoration-red-800">[ Initialize Profile ]</a>
                            </div>
                        @endif
                        <div class="text-[9px] text-red-900/50 uppercase tracking-[0.2em]">
                            System ID: {{ Str::upper(Str::random(8)) }}-{{ request()->ip() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink {
            50% { opacity: 0 }
        }
        .typing-effect {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid transparent;
            animation: typing 2.5s steps(30, end), blink 0.75s step-end infinite;
            max-width: 100%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('button[type="submit"]');
                const staticText = document.getElementById('btn-static');
                const loadingText = document.getElementById('btn-loading');
                
                if (btn.classList.contains('is-loading')) {
                    e.preventDefault();
                    return;
                }

                btn.classList.add('is-loading');
                staticText.classList.add('hidden');
                loadingText.classList.remove('hidden');
                
                // Allow the animation to be seen briefly if form submission is too fast (optional, but good for UX)
                // However, for standard submission, we just let it proceed.
            });
        });
    </script>
</body>
</html>
