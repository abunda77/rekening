{{-- Custom Sun/Light Mode Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-sun shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="sun-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#fbbf24" />
            <stop offset="100%" stop-color="#f59e0b" />
        </linearGradient>
    </defs>
    <circle stroke="url(#sun-gradient)" cx="12" cy="12" r="5" />
    <line stroke="url(#sun-gradient)" x1="12" y1="1" x2="12" y2="3" />
    <line stroke="url(#sun-gradient)" x1="12" y1="21" x2="12" y2="23" />
    <line stroke="url(#sun-gradient)" x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
    <line stroke="url(#sun-gradient)" x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
    <line stroke="url(#sun-gradient)" x1="1" y1="12" x2="3" y2="12" />
    <line stroke="url(#sun-gradient)" x1="21" y1="12" x2="23" y2="12" />
    <line stroke="url(#sun-gradient)" x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
    <line stroke="url(#sun-gradient)" x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
</svg>
