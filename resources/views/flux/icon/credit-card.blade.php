{{-- Custom Credit Card/Kartu ATM Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-credit-card shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="card-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#8b5cf6" />
            <stop offset="100%" stop-color="#7c3aed" />
        </linearGradient>
    </defs>
    <rect stroke="url(#card-gradient)" x="1" y="4" width="22" height="16" rx="2" ry="2" />
    <line stroke="url(#card-gradient)" x1="1" y1="10" x2="23" y2="10" />
</svg>
