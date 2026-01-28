{{-- Custom Building Library/Rekening Bank Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-building-library shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="bank-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#06b6d4" />
            <stop offset="100%" stop-color="#0891b2" />
        </linearGradient>
    </defs>
    <path stroke="url(#bank-gradient)" d="M3 21h18" />
    <path stroke="url(#bank-gradient)" d="M3 10h18" />
    <path stroke="url(#bank-gradient)" d="M5 6l7-3 7 3" />
    <path stroke="url(#bank-gradient)" d="M4 10v11" />
    <path stroke="url(#bank-gradient)" d="M20 10v11" />
    <path stroke="url(#bank-gradient)" d="M8 14v3" />
    <path stroke="url(#bank-gradient)" d="M12 14v3" />
    <path stroke="url(#bank-gradient)" d="M16 14v3" />
</svg>
