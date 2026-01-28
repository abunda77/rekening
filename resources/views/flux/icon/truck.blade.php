{{-- Custom Truck/Shipment Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-truck shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="truck-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#ec4899" />
            <stop offset="100%" stop-color="#db2777" />
        </linearGradient>
    </defs>
    <path stroke="url(#truck-gradient)" d="M1 3h15v13H1z" />
    <path stroke="url(#truck-gradient)" d="M16 8h4l3 3v5h-7V8z" />
    <circle stroke="url(#truck-gradient)" cx="5.5" cy="18.5" r="2.5" />
    <circle stroke="url(#truck-gradient)" cx="18.5" cy="18.5" r="2.5" />
</svg>
