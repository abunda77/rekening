{{-- Key Icon for Permissions Menu --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-key shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="key-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3b82f6" />
            <stop offset="100%" stop-color="#8b5cf6" />
        </linearGradient>
    </defs>
    <circle stroke="url(#key-gradient)" cx="7.5" cy="15.5" r="5.5" />
    <path stroke="url(#key-gradient)" d="m21 2-9.6 9.6" />
    <path stroke="url(#key-gradient)" d="m15 5 3 3" />
    <path stroke="url(#key-gradient)" d="m19 9 3 3" />
</svg>
