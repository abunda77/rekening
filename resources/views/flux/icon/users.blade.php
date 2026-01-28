{{-- Custom Users/Agent Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-users shrink-0 w-5 h-5 transition-all duration-300']) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
    <defs>
        <linearGradient id="users-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#10b981" />
            <stop offset="100%" stop-color="#059669" />
        </linearGradient>
    </defs>
    <path stroke="url(#users-gradient)" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
    <circle stroke="url(#users-gradient)" cx="9" cy="7" r="4" />
    <path stroke="url(#users-gradient)" d="M22 21v-2a4 4 0 0 0-3-3.87" />
    <path stroke="url(#users-gradient)" d="M16 3.13a4 4 0 0 1 0 7.75" />
</svg>
