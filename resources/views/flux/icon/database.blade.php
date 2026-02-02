{{-- Database Icon for Backup Database Menu --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-database shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="database-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3b82f6" />
            <stop offset="100%" stop-color="#8b5cf6" />
        </linearGradient>
    </defs>
    <ellipse stroke="url(#database-gradient)" cx="12" cy="5" rx="9" ry="3" />
    <path stroke="url(#database-gradient)" d="M3 5V19A9 3 0 0 0 21 19V5" />
    <path stroke="url(#database-gradient)" d="M3 12A9 3 0 0 0 21 12" />
</svg>
