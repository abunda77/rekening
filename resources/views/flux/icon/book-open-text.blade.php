{{-- Custom Book Open Text/Documentation Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-book shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="book-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#14b8a6" />
            <stop offset="100%" stop-color="#0d9488" />
        </linearGradient>
    </defs>
    <path stroke="url(#book-gradient)" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
    <path stroke="url(#book-gradient)" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
</svg>
