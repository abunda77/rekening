<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900">
    <!-- Animated Background Grid -->
    <div class="absolute inset-0 z-0">
        <!-- Gradient Mesh Background -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-600/20 via-purple-600/10 to-transparent"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_var(--tw-gradient-stops))] from-cyan-600/20 via-transparent to-transparent"></div>

        <!-- Animated Grid Pattern -->
        <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>

        <!-- Floating Orbs -->
        <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-blue-500/30 rounded-full mix-blend-screen filter blur-3xl animate-float-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full mix-blend-screen filter blur-3xl animate-float-medium"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyan-500/20 rounded-full mix-blend-screen filter blur-3xl animate-float-fast"></div>

        <!-- Particle Effects -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-1/5 w-2 h-2 bg-blue-400/60 rounded-full animate-particle-1"></div>
            <div class="absolute top-40 right-1/4 w-1 h-1 bg-purple-400/60 rounded-full animate-particle-2"></div>
            <div class="absolute bottom-32 left-1/3 w-1.5 h-1.5 bg-cyan-400/60 rounded-full animate-particle-3"></div>
            <div class="absolute bottom-20 right-1/5 w-2 h-2 bg-indigo-400/60 rounded-full animate-particle-4"></div>
        </div>
    </div>

    <!-- Glass Card Container -->
    <div class="relative z-10 w-full max-w-md mx-4">
        <!-- Glow Effect Behind Card -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-600 rounded-3xl blur opacity-30 animate-pulse-slow"></div>

        <!-- Main Glass Card -->
        <div class="relative bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl shadow-black/50 overflow-hidden">
            <!-- Shimmer Effect -->
            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent translate-x-[-200%] animate-shimmer"></div>

            <!-- Card Content -->
            <div class="relative p-8 md:p-10">
                <!-- Logo Section -->
                <div class="text-center mb-10">
                    <div class="relative inline-block mb-6">
                        <!-- Logo Glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl blur-lg opacity-50 animate-pulse-slow"></div>
                        <!-- Logo Icon -->
                        <div class="relative w-16 h-16 bg-gradient-to-br from-blue-500 via-purple-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold bg-gradient-to-r from-white via-blue-200 to-cyan-200 bg-clip-text text-transparent tracking-tight mb-2">
                        Agent Portal
                    </h1>
                    <p class="text-slate-400 font-medium text-sm">Rekening Management System</p>
                </div>

                <!-- Login Form -->
                <form wire:submit="login" class="space-y-6">
                    <!-- Agent Code Input -->
                    <div class="group">
                        <label for="agent_code" class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-blue-400">
                            Kode Agen
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500 transition-colors group-focus-within:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="agent_code"
                                wire:model="agent_code"
                                required
                                autofocus
                                class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                                       focus:outline-none focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20
                                       transition-all duration-300 backdrop-blur-sm hover:bg-white/10"
                                placeholder="Masukkan kode agen">
                            <!-- Focus Glow -->
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-cyan-500/0 opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>
                        <p class="mt-2 text-xs text-slate-400 italic flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Kode agen bersifat case-sensitive
                        </p>
                        @error('agent_code')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- PIN Input -->
                    <div class="group">
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2 transition-colors group-focus-within:text-blue-400">
                            PIN
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-500 transition-colors group-focus-within:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                type="password"
                                id="password"
                                wire:model="password"
                                required
                                class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500
                                       focus:outline-none focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20
                                       transition-all duration-300 backdrop-blur-sm hover:bg-white/10"
                                placeholder="••••••••">
                            <!-- Focus Glow -->
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-cyan-500/0 opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" wire:model="remember" class="peer sr-only">
                                <div class="w-5 h-5 bg-white/5 border border-white/20 rounded-md transition-all peer-checked:bg-blue-500 peer-checked:border-blue-500 group-hover:border-white/40"></div>
                                <svg class="absolute inset-0 w-5 h-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="group relative w-full">
                        <!-- Button Glow -->
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-600 rounded-xl blur opacity-50 group-hover:opacity-75 transition duration-300"></div>
                        <!-- Button Content -->
                        <div class="relative w-full py-4 px-6 bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-600 rounded-xl text-white font-semibold shadow-lg shadow-blue-500/25 overflow-hidden">
                            <!-- Shimmer -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>

                            <span wire:loading.remove class="relative flex items-center justify-center gap-2">
                                Masuk
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>

                            <span wire:loading class="relative flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </div>
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-white/10 text-center">
                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} Rekening App. Seluruh hak cipta dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Animations -->
    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            33% { transform: translateY(-30px) translateX(20px) scale(1.05); }
            66% { transform: translateY(20px) translateX(-20px) scale(0.95); }
        }
        @keyframes float-medium {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            50% { transform: translateY(40px) translateX(30px) scale(1.1); }
        }
        @keyframes float-fast {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            25% { transform: translateY(-20px) translateX(10px) scale(1.02); }
            75% { transform: translateY(20px) translateX(-10px) scale(0.98); }
        }
        @keyframes shimmer {
            100% { transform: translateX(200%); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }
        @keyframes particle {
            0%, 100% { transform: translateY(0) opacity(0.6); }
            50% { transform: translateY(-20px) opacity(1); }
        }
        @keyframes particle-2 {
            0%, 100% { transform: translateY(0) translateX(0) opacity(0.6); }
            50% { transform: translateY(-30px) translateX(10px) opacity(1); }
        }
        @keyframes particle-3 {
            0%, 100% { transform: translateY(0) translateX(0) opacity(0.6); }
            50% { transform: translateY(-15px) translateX(-15px) opacity(1); }
        }
        @keyframes particle-4 {
            0%, 100% { transform: translateY(0) translateX(0) opacity(0.6); }
            50% { transform: translateY(-25px) translateX(-5px) opacity(1); }
        }

        .animate-float-slow {
            animation: float-slow 12s ease-in-out infinite;
        }
        .animate-float-medium {
            animation: float-medium 10s ease-in-out infinite;
        }
        .animate-float-fast {
            animation: float-fast 8s ease-in-out infinite;
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }
        .animate-particle-1 {
            animation: particle 6s ease-in-out infinite;
        }
        .animate-particle-2 {
            animation: particle-2 7s ease-in-out infinite;
            animation-delay: 1s;
        }
        .animate-particle-3 {
            animation: particle-3 8s ease-in-out infinite;
            animation-delay: 2s;
        }
        .animate-particle-4 {
            animation: particle-4 5s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        /* Glass Effect Enhancement */
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            box-shadow:
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
        }
    </style>
</div>
