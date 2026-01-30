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
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }
        .text-gradient {
            background: linear-gradient(to right, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-gradient-2 {
            background: linear-gradient(to right, #22d3ee, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#020617] text-slate-300 min-h-screen selection:bg-blue-500 selection:text-white overflow-x-hidden">
    
    <!-- Background Gradients -->
    <div class="fixed inset-0 -z-10 h-full w-full pointer-events-none">
        <div class="absolute top-0 left-1/4 w-[600px] h-[500px] bg-blue-600/20 blur-[130px] rounded-full mix-blend-screen opacity-60"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[500px] bg-purple-600/10 blur-[130px] rounded-full mix-blend-screen opacity-50"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-100"></div>
    </div>

    <!-- Navigation -->
    <nav class="glass-nav fixed w-full z-50 top-0 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 ring-1 ring-white/20">
                         <!-- Logo Icon -->
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="font-bold text-xl text-white tracking-tight">{{ config('app.name', 'Rekening') }}</span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#features" class="hover:text-white transition-colors duration-200">Features</a>
                        <a href="#about" class="hover:text-white transition-colors duration-200">About</a>
                        <a href="#contact" class="hover:text-white transition-colors duration-200">Contact</a>
                    </div>
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

    <!-- Hero Section -->
    <main class="pt-32 pb-20 lg:pt-48 lg:pb-32 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-medium mb-8 animate-fade-in-up">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                New Generation Banking
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-white mb-8 leading-tight">
                Manage your wealth with <br>
                <span class="text-gradient">Integrity & Speed</span>
            </h1>
            
            <p class="mt-4 text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Experience the future of financial management. Secure, fast, and intuitively designed for the modern era. Join thousands of satisfied users today.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                 @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white text-slate-900 font-bold text-lg hover:bg-slate-100 transition-all shadow-xl shadow-white/10 transform hover:-translate-y-1">
                        Create Free Account
                    </a>
                @endif
                <a href="#learn-more" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white/5 text-white font-bold text-lg hover:bg-white/10 border border-white/10 transition-all backdrop-blur-sm">
                    Learn More
                </a>
            </div>

            <!-- Stats / Cards Preview -->
            <div class="mt-20 relative mx-auto max-w-5xl">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-3xl blur opacity-20"></div>
                <div class="relative glass-card rounded-2xl border border-white/10 p-2 overflow-hidden shadow-2xl">
                     <!-- Mockup Content -->
                     <img src="https://ui.aceternity.com/_next/image?url=%2Fdashboard.png&w=3840&q=75" alt="Dashboard Preview" class="rounded-xl w-full h-auto opacity-90 hover:opacity-100 transition-opacity duration-500 shadow-inner">
                     <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#020617] to-transparent"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Grid -->
    <section id="features" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
             <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">Everything you need</h2>
                <p class="mt-4 text-slate-400 text-lg">Powerful features to help you grow your business.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 hover:border-blue-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10 group">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Bank-Grade Security</h3>
                    <p class="text-slate-400 leading-relaxed">Your data is encrypted and protected with industry-leading security standards.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 hover:border-purple-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/10 group">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                         <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Lightning Fast</h3>
                    <p class="text-slate-400 leading-relaxed">Experience zero latency with our optimized platform structure and global CDN.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card p-8 rounded-2xl border border-white/5 hover:border-cyan-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10 group">
                    <div class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                         <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Real-time Analytics</h3>
                    <p class="text-slate-400 leading-relaxed">Track your progress with detailed analytics and instant reporting features.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/10 bg-[#020617] pt-16 pb-8 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                     <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-bold text-lg text-white">Rekening</span>
                    </div>
                    <p class="text-slate-400 max-w-sm">Making financial management easy, secure, and accessible for everyone anywhere in the world.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Product</h4>
                    <ul class="space-y-4 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Changelog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Company</h4>
                    <ul class="space-y-4 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <div class="flex gap-6">
                     <a href="#" class="text-slate-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg></a>
                     <a href="#" class="text-slate-500 hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 C9.673 2.013 10.033 2 12.481 2h.08zm-5.77 2c-2.69 0-3.35.03-4.155.334a2.91 2.91 0 00-1.07 1.07c-.305.805-.334 1.465-.334 4.155v.234c0 2.69.03 3.35.334 4.155.195.51.56.875 1.07 1.07.805.305 1.465.334 4.155.334h.234c2.69 0 3.35-.03 4.155-.334a2.91 2.91 0 001.07-1.07c.305-.805.334-1.465.334-4.155v-.234c0-2.69-.03-3.35-.334-4.155a2.91 2.91 0 00-1.07-1.07c-.805-.305-1.465-.334-4.155-.334H6.545zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 2a3.135 3.135 0 100 6.27 3.135 3.135 0 000-6.27zm5.534-4.27a1.187 1.187 0 110 2.374 1.187 1.187 0 010-2.374z" clip-rule="evenodd" /></svg></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
