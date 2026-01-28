<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        <style>
            /* Elegant Icon Hover Effects */
            .sidebar-icon-wrapper {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                border-radius: 6px;
                background: transparent;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .sidebar-icon-wrapper svg {
                width: 20px;
                height: 20px;
            }

            /* Light mode hover */
            :root:not(.dark) .sidebar-icon-wrapper:hover {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(139, 92, 246, 0.1) 100%);
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
                transform: translateY(-1px) scale(1.05);
            }

            /* Dark mode hover */
            .dark .sidebar-icon-wrapper:hover {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(139, 92, 246, 0.15) 100%);
                box-shadow: 0 2px 10px rgba(59, 130, 246, 0.25);
                transform: translateY(-1px) scale(1.05);
            }

            /* Icon glow animation */
            .sidebar-icon-wrapper svg {
                filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));
                transition: all 0.3s ease;
            }

            :root:not(.dark) .sidebar-icon-wrapper:hover svg {
                filter: drop-shadow(0 1px 3px rgba(59, 130, 246, 0.3));
                transform: scale(1.15);
            }

            .dark .sidebar-icon-wrapper:hover svg {
                filter: drop-shadow(0 1px 3px rgba(139, 92, 246, 0.4));
                transform: scale(1.15);
            }

            /* Active state styling */
            .sidebar-icon-wrapper.active {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(139, 92, 246, 0.1) 100%);
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
            }

            .dark .sidebar-icon-wrapper.active {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(139, 92, 246, 0.15) 100%);
                box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
            }

            /* Ripple effect on click */
            .sidebar-icon-wrapper::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 6px;
                background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .sidebar-icon-wrapper:active::after {
                opacity: 1;
            }

            /* Navigation item text hover effect */
            .nav-item-text {
                transition: color 0.3s ease, text-shadow 0.3s ease;
            }

            :root:not(.dark) .flux\:sidebar\.item:hover .nav-item-text {
                color: #3b82f6;
                text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
            }

            .dark .flux\:sidebar\.item:hover .nav-item-text {
                color: #8b5cf6;
                text-shadow: 0 0 10px rgba(139, 92, 246, 0.4);
            }
        </style>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                @include('flux::icon.dashboard')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Dashboard') }}</span>
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Rekening')" class="grid">
                    <flux:sidebar.item :href="route('rekening.agents')" :current="request()->routeIs('rekening.agents')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.agents') ? 'active' : '' }}">
                                @include('flux::icon.users')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Agent') }}</span>
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('rekening.customers')" :current="request()->routeIs('rekening.customers')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.customers') ? 'active' : '' }}">
                                @include('flux::icon.user-circle')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Customer') }}</span>
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('rekening.accounts')" :current="request()->routeIs('rekening.accounts')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.accounts') ? 'active' : '' }}">
                                @include('flux::icon.building-library')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Rekening Bank') }}</span>
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('rekening.cards')" :current="request()->routeIs('rekening.cards')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.cards') ? 'active' : '' }}">
                                @include('flux::icon.credit-card')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Kartu ATM') }}</span>
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Support')" class="grid">
                    <flux:sidebar.item :href="route('rekening.complaints')" :current="request()->routeIs('rekening.complaints')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.complaints') ? 'active' : '' }}">
                                @include('flux::icon.chat-bubble-left-right')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Help Desk') }}</span>
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('rekening.shipments')" :current="request()->routeIs('rekening.shipments')" wire:navigate>
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper {{ request()->routeIs('rekening.shipments') ? 'active' : '' }}">
                                @include('flux::icon.truck')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Shipment') }}</span>
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    <x-slot:icon>
                        <div class="sidebar-icon-wrapper">
                            @include('flux::icon.folder-git-2')
                        </div>
                    </x-slot:icon>
                    <span class="nav-item-text">{{ __('Repository') }}</span>
                </flux:sidebar.item>

                <flux:sidebar.item href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    <x-slot:icon>
                        <div class="sidebar-icon-wrapper">
                            @include('flux::icon.book-open-text')
                        </div>
                    </x-slot:icon>
                    <span class="nav-item-text">{{ __('Documentation') }}</span>
                </flux:sidebar.item>

                <div x-data="{ darkMode: document.documentElement.classList.contains('dark') }" x-init="darkMode = document.documentElement.classList.contains('dark')">
                    <flux:sidebar.item x-show="!darkMode" x-on:click="darkMode = true; document.documentElement.classList.add('dark'); localStorage.theme = 'dark';" class="cursor-pointer">
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper">
                                @include('flux::icon.moon')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Dark Mode') }}</span>
                    </flux:sidebar.item>

                    <flux:sidebar.item x-show="darkMode" x-on:click="darkMode = false; document.documentElement.classList.remove('dark'); localStorage.theme = 'light';" class="cursor-pointer" style="display: none;">
                        <x-slot:icon>
                            <div class="sidebar-icon-wrapper">
                                @include('flux::icon.sun')
                            </div>
                        </x-slot:icon>
                        <span class="nav-item-text">{{ __('Light Mode') }}</span>
                    </flux:sidebar.item>
                </div>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
