{{-- Custom Moon/Dark Mode Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-moon shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="moon-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#64748b" />
            <stop offset="100%" stop-color="#475569" />
        </linearGradient>
    </defs>
    <path stroke="url(#moon-gradient)" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
</svg>
