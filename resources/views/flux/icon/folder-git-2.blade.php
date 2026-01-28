{{-- Custom Folder Git 2/Repository Icon with Gradient --}}

@props([
    'size' => 24,
])

<svg
    {{ $attributes->merge(['class' => 'icon-folder-git shrink-0 w-5 h-5 transition-all duration-300']) }}
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
        <linearGradient id="git-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#6366f1" />
            <stop offset="100%" stop-color="#4f46e5" />
        </linearGradient>
    </defs>
    <path stroke="url(#git-gradient)" d="M9 20H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v5" />
    <circle stroke="url(#git-gradient)" cx="13" cy="12" r="2" />
    <path stroke="url(#git-gradient)" d="M18 19c-2.8 0-5-2.2-5-5v8" />
    <circle stroke="url(#git-gradient)" cx="20" cy="19" r="2" />
</svg>
