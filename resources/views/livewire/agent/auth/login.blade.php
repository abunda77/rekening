<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-[#020617] font-mono selection:bg-green-500 selection:text-black">
    
    <!-- CRT/Scanline Effect Overlay -->
    <div class="pointer-events-none fixed inset-0 z-50 mix-blend-overlay opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
    <div class="pointer-events-none fixed inset-0 z-40 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"></div>

    <!-- Background Map/Grid -->
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1f2937_1px,transparent_1px),linear-gradient(to_bottom,#1f2937_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-[#020617] via-transparent to-transparent"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-20 w-full max-w-lg p-4">
        
        <!-- Header / Warning -->
        <div class="text-center mb-8 space-y-2">
            <div class="inline-block border border-red-500/50 bg-red-500/10 px-3 py-1 text-xs text-red-400 tracking-[0.2em] uppercase animate-pulse">
                Restricted Access // Level 5 Clearance
            </div>
            
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/50 backdrop-blur-md border border-slate-700/50 shadow-2xl relative overflow-hidden group">
            
            <!-- Corner Accents -->
            <div class="absolute top-0 left-0 w-4 h-4 border-l-2 border-t-2 border-blue-500"></div>
            <div class="absolute top-0 right-0 w-4 h-4 border-r-2 border-t-2 border-blue-500"></div>
            <div class="absolute bottom-0 left-0 w-4 h-4 border-l-2 border-b-2 border-blue-500"></div>
            <div class="absolute bottom-0 right-0 w-4 h-4 border-r-2 border-b-2 border-blue-500"></div>

            <div class="p-8 md:p-12 relative">
                
                <!-- Logo Section -->
                <div class="flex flex-col items-center mb-10">
                    <div class="w-20 h-20 mb-6 relative">
                         <!-- Custom Agency Logo -->
                        <svg class="w-full h-full text-blue-500 drop-shadow-[0_0_15px_rgba(59,130,246,0.5)]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50 5L85 25V75L50 95L15 75V25L50 5Z" stroke="currentColor" stroke-width="2" Fill="none"/> <!-- Outer Shield -->
                            <circle cx="50" cy="50" r="25" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 2" class="animate-[spin_10s_linear_infinite] origin-center opacity-70"/> <!-- Radar Circle -->
                            <path d="M50 25V50M50 50L65 65" stroke="currentColor" stroke-width="1.5"/> <!-- Radar Sweep -->
                             <circle cx="50" cy="50" r="5" fill="currentColor" class="animate-pulse"/> <!-- Center Dot -->
                             <!-- Tech details -->
                            <path d="M15 25L25 35M85 25L75 35M15 75L25 65M85 75L75 65" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl text-white font-bold tracking-widest uppercase">Agent Portal</h1>
                    <div class="text-xs text-blue-400 mt-1 tracking-widest">SECURE LOGIN TERMINAL-01</div>
                </div>

                <!-- Form -->
                <form wire:submit="login" class="space-y-6">
                    
                    <!-- Code Input -->
                    <div class="space-y-1">
                        <label class="text-xs text-blue-400 uppercase tracking-widest pl-1">Agent Identity Code</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-500">
                                >
                            </div>
                            <input type="text" 
                                   wire:model="agent_code" 
                                   required 
                                   autofocus
                                   class="block w-full bg-slate-950/50 border border-slate-700 text-slate-300 py-3 pl-8 pr-4 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-slate-900/80 transition-all font-mono tracking-widest"
                                   placeholder="ENTER CODE">
                        </div>
                         @error('agent_code') 
                            <div class="text-red-500 text-xs mt-1 font-bold tracking-wide animate-pulse">
                                [ERROR]: {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1">
                        <label class="text-xs text-blue-400 uppercase tracking-widest pl-1">Access Key</label>
                        <div class="relative group">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 group-focus-within:text-blue-500">
                                >
                            </div>
                            <input type="password" 
                                   wire:model="password" 
                                   required 
                                   class="block w-full bg-slate-950/50 border border-slate-700 text-slate-300 py-3 pl-8 pr-4 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-slate-900/80 transition-all font-mono tracking-widest"
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
                            <input type="checkbox" wire:model="remember" class="form-checkbox h-4 w-4 text-blue-600 border-slate-600 bg-slate-900 rounded focus:ring-blue-500 focus:ring-offset-slate-900">
                            <span class="text-xs text-slate-400 uppercase tracking-wider">Maintain Session</span>
                        </label>
                        <a href="#" class="text-xs text-slate-500 hover:text-blue-400 uppercase tracking-wider transition-colors">[ Forgot Key? ]</a>
                    </div>

                    <!-- Terminal Typing Button -->
                    <button type="submit" class="w-full relative group overflow-hidden mt-6 h-14 bg-slate-950 border border-green-500/30 hover:border-green-500/80 transition-all duration-300">
                        <div class="absolute inset-0 bg-green-500/5 group-hover:bg-green-500/10 transition-colors"></div>
                        
                        <!-- Static State -->
                        <div wire:loading.remove class="flex items-center justify-center h-full">
                            <span class="text-green-500 font-bold tracking-[0.2em] text-sm uppercase group-hover:animate-pulse">
                                >> INITIATE UPLINK
                            </span>
                        </div>

                        <!-- Scanline on Hover -->
                        <div class="absolute top-0 left-0 w-full h-[2px] bg-green-500/50 -translate-y-full group-hover:translate-y-[60px] transition-transform duration-1000 ease-linear"></div>

                        <!-- Typing Effect State -->
                        <div wire:loading class="absolute inset-0 flex items-center justify-center bg-slate-950 z-10">
                            <div class="flex items-center">
                                <span class="text-green-400 font-mono text-xs md:text-sm typing-effect">
                                    ESTABLISHING SECURE CONNECTION...
                                </span>
                                <span class="w-2 h-4 bg-green-500 ml-1 animate-blink"></span>
                            </div>
                        </div>

                        <!-- Error State (If errors exist in validation bag) -->
                        @if ($errors->any())
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-950 z-20 border border-red-500/50">
                                <span class="text-red-500 font-bold tracking-widest text-sm animate-pulse">
                                    >> ACCESS DENIED <<
                                </span>
                            </div>
                        @endif
                    </button>
                    
                </form>

                <div class="mt-8 text-center">
                    <div class="text-[10px] text-slate-600 uppercase tracking-widest">
                        Unauthorized access is a federal offense.<br>
                        IP Address Logged: {{ request()->ip() }}
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
            border-right: 0px solid transparent;
            animation: typing 2.5s steps(30, end);
            max-width: 100%;
        }
        .animate-blink {
            animation: blink 1s step-end infinite;
        }
    </style>
</div>
