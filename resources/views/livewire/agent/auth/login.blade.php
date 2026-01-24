<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-50">
    <!-- Background Pattern -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-sky-100"></div>
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(#3b82f6 0.5px, transparent 0.5px); background-size: 24px 24px;"></div>
        
        <!-- Decorative blobs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md p-8 bg-white/30 backdrop-blur-xl border border-white/40 rounded-2xl shadow-xl ring-1 ring-black/5">
        <div class="mb-8 text-center">
            <div class="mx-auto h-12 w-12 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Agent Portal</h1>
            <p class="text-slate-600 mt-2 font-medium">Rekening Management System</p>
        </div>

        <form wire:submit="login" class="space-y-6">
            <div>
                <div class="flex items-center space-x-4">
                    <label for="agent_code" class="w-1/3 text-sm font-semibold text-slate-700">Agent Code</label>
                    <div class="relative w-2/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="agent_code" wire:model="agent_code" required autofocus
                            class="pl-10 w-full px-4 py-3 bg-white/60 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none placeholder-slate-400 text-slate-800 backdrop-blur-sm"
                            placeholder="Enter your agent code">
                        <p class="mt-1 text-xs text-slate-500 italic">* Agent code is case-sensitive</p>
                    </div>
                </div>
                @error('agent_code') <span class="text-red-500 text-sm mt-1 block font-medium ml-[33.3333%] pl-4">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex items-center space-x-4">
                    <label for="password" class="w-1/3 text-sm font-semibold text-slate-700">Password</label>
                    <div class="relative w-2/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="password" id="password" wire:model="password" required
                            class="pl-10 w-full px-4 py-3 bg-white/60 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none placeholder-slate-400 text-slate-800 backdrop-blur-sm"
                            placeholder="••••••••">
                    </div>
                </div>
                @error('password') <span class="text-red-500 text-sm mt-1 block font-medium ml-[33.3333%] pl-4">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 cursor-pointer select-none">
                    <input type="checkbox" wire:model="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-slate-600 font-medium">Remember me</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 transform transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-sm tracking-wide">
                <span wire:loading.remove>Sign In</span>
                <span wire:loading class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-200/50 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Rekening App. All rights reserved.
        </div>
    </div>
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</div>
