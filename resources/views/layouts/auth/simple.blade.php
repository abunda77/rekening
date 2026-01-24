<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
             .glass-panel {
                 background: rgba(15, 23, 42, 0.6);
                 backdrop-filter: blur(12px);
                 -webkit-backdrop-filter: blur(12px);
                 border: 1px solid rgba(255, 255, 255, 0.08);
                 box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
             }
        </style>
    </head>
    <body class="min-h-screen antialiased bg-[#020617] text-white selection:bg-blue-500 selection:text-white overflow-hidden relative">
        <!-- Background Effects -->
        <div class="fixed inset-0 -z-10 h-full w-full pointer-events-none">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-600/20 blur-[100px] rounded-full mix-blend-screen opacity-50"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[400px] bg-indigo-600/10 blur-[90px] rounded-full mix-blend-screen opacity-40"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-100"></div>
        </div>

        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 relative z-10">
            <div class="glass-panel w-full max-w-[400px] flex flex-col gap-6 rounded-2xl p-8 shadow-2xl animate-in fade-in zoom-in duration-500">
                 <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium transition-transform hover:scale-105 duration-300" wire:navigate>
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/30 ring-1 ring-white/20">
                        <x-app-logo-icon class="size-8 text-white fill-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                
                <div class="flex flex-col gap-6 mt-2">
                    {{ $slot }}
                </div>
            </div>
             <div class="text-xs text-slate-500 mt-4">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
        @fluxScripts
    </body>
</html>
