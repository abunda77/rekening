{{-- Custom User Circle/Customer Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-user-circle shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="user-circle-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#f59e0b" />
            <stop offset="100%" stop-color="#d97706" />
        </linearGradient>
    </defs>
    <circle stroke="url(#user-circle-gradient)" cx="12" cy="12" r="10" />
    <path stroke="url(#user-circle-gradient)" d="M8 14s1.5 2 4 2 4-2 4-2" />
    <line stroke="url(#user-circle-gradient)" x1="9" y1="9" x2="9.01" y2="9" />
    <line stroke="url(#user-circle-gradient)" x1="15" y1="9" x2="15.01" y2="9" />
</svg>
