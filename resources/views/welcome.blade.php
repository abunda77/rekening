<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
@include('partials.head', ['title' => 'Ruang Kerja — semua catatan dalam satu alur'])
<script>
    // Bridge: the app sidebar stores its choice in localStorage.theme, Flux stores flux.appearance.
    try {
        const stored = window.localStorage.getItem('theme');
        if (stored) window.Flux.applyAppearance(stored === 'dark' ? 'dark' : 'light');
    } catch {}
</script>
<meta name="description" content="Ruang kerja untuk merapikan catatan, memantau progres, dan menutup pekerjaan tanpa berpindah aplikasi.">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=jetbrains-mono:500|space-grotesk:500,600&display=swap" rel="stylesheet">
<style>
/* Hallmark · macrostructure: Bento Grid · tone: cool engineered, calm · anchor hue: cobalt 256
 * genre: modern-minimal · theme: Cobalt (light + dark) · nav: N9 edge-aligned minimal · footer: Ft2 inline rule single line
 * enrichment: none (typography only) · diversification: light/dark paper / grotesk-sans / cool accent
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 */
@import url('/tokens.css');

html, body { overflow-x: clip; }
body { margin: 0; background: var(--color-paper); color: var(--color-ink); font-family: var(--font-body); font-size: var(--text-md); line-height: 1.55; -webkit-font-smoothing: antialiased; transition: background-color var(--dur-short) var(--ease-out), color var(--dur-short) var(--ease-out); }
*, *::before, *::after { box-sizing: border-box; }
a { color: inherit; }
.shell { width: min(100% - (var(--space-md) * 2), 74rem); margin-inline: auto; }

/* nav — N9 edge-aligned minimal */
.nav { display: flex; align-items: center; justify-content: space-between; gap: var(--space-md); padding-block: var(--space-lg); border-bottom: var(--rule-thin) solid var(--color-rule); }
.nav__brand { font-family: var(--font-display); font-size: var(--text-xl); font-weight: 500; letter-spacing: -0.02em; text-decoration: none; white-space: nowrap; }
.nav__brand:hover { color: var(--color-accent); }

/* theme toggle — plain button, Flux appearance API */
.theme-toggle { display: inline-flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border: var(--rule-thin) solid var(--color-control-border); border-radius: var(--radius-sm); background: transparent; color: var(--color-ink-soft); cursor: pointer; transition: border-color var(--dur-short) var(--ease-out), color var(--dur-short) var(--ease-out), background-color var(--dur-short) var(--ease-out); }
.theme-toggle:hover { border-color: var(--color-accent); color: var(--color-accent); background: var(--color-accent-soft); }
.theme-toggle:active { transform: translateY(1px); }
.theme-toggle:focus-visible { outline: 3px solid var(--color-focus); outline-offset: 3px; }
.theme-toggle__icon { display: inline-flex; }
.theme-toggle__icon svg { width: 1.125rem; height: 1.125rem; }
:root:not(.dark) .theme-toggle__icon--sun { display: none; }
.dark .theme-toggle__icon--moon { display: none; }

/* hero — left-biased */
.hero { padding-block: clamp(var(--space-3xl), 9vw, var(--space-4xl)) var(--space-2xl); }
.eyebrow { margin: 0 0 var(--space-md); color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; animation: fade-up var(--dur-reveal) var(--ease-out) both; }
.hero__title { max-width: 22ch; margin: 0 0 var(--space-lg); font-family: var(--font-display); font-size: var(--text-display); font-weight: 500; font-style: normal; letter-spacing: -0.035em; line-height: 0.98; min-width: 0; overflow-wrap: anywhere; }
.hero__lede { max-width: 52ch; margin: 0; color: var(--color-ink-soft); font-size: var(--text-lg); line-height: 1.5; animation: fade-up var(--dur-reveal) var(--ease-out) 420ms both; }

/* title reveal — one word at a time */
.word { display: inline-block; animation: word-in var(--dur-reveal) var(--ease-out) both; }
.word:nth-child(1) { animation-delay: 70ms; }
.word:nth-child(2) { animation-delay: 130ms; }
.word:nth-child(3) { animation-delay: 190ms; }
.word:nth-child(4) { animation-delay: 250ms; }
.word:nth-child(5) { animation-delay: 310ms; }
.word:nth-child(6) { animation-delay: 370ms; }
.word:nth-child(7) { animation-delay: 430ms; }

/* bento grid */
.bento { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: var(--space-sm); margin-bottom: var(--space-3xl); }
.cell { display: flex; flex-direction: column; justify-content: space-between; gap: var(--space-lg); padding: var(--space-lg); border: var(--rule-thin) solid var(--color-rule); border-radius: var(--radius-md); background: var(--color-paper-raised); animation: fade-up var(--dur-reveal) var(--ease-out) both; transition: border-color var(--dur-short) var(--ease-out), background-color var(--dur-short) var(--ease-out); }
.cell:nth-child(1) { animation-delay: 340ms; }
.cell:nth-child(2) { animation-delay: 400ms; }
.cell:nth-child(3) { animation-delay: 460ms; }
.cell:nth-child(4) { animation-delay: 520ms; }
.cell:nth-child(5) { animation-delay: 580ms; }
.cell:nth-child(6) { animation-delay: 640ms; }
.cell:nth-child(7) { animation-delay: 700ms; }
.cell__label { color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; }
.cell__title { margin: 0; font-family: var(--font-display); font-size: var(--text-xl); font-weight: 500; letter-spacing: -0.02em; line-height: 1.1; min-width: 0; overflow-wrap: anywhere; }
.cell__copy { margin: var(--space-xs) 0 0; color: var(--color-ink-soft); font-size: var(--text-sm); }
.cell--lead { grid-column: span 2; grid-row: span 2; }
.cell--wide { grid-column: span 2; }
.cell--lead .cell__title { font-size: var(--text-2xl); }
.cell--dark { border-color: var(--color-graphite); background: var(--color-graphite); color: var(--color-graphite-ink); }
.cell--dark .cell__label { color: var(--color-graphite-muted); }
.cell--dark .cell__copy { color: var(--color-graphite-muted); }
.cell:hover { border-color: var(--color-rule-strong); }

/* status strip — Flux badges inside the lead tile */
.readout { display: flex; flex-wrap: wrap; gap: var(--space-xs); }

/* footer — Ft2 inline single line */
.foot { display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-xs) var(--space-md); padding-block: var(--space-lg) var(--space-xl); border-top: var(--rule-thin) solid var(--color-rule); color: var(--color-muted); font-size: var(--text-sm); animation: fade-up var(--dur-reveal) var(--ease-out) 760ms both; }
.foot__sep { color: var(--color-rule-strong); }
.foot__end { margin-left: auto; font-family: var(--font-mono); font-size: var(--text-xs); }

@keyframes fade-up { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes word-in { from { opacity: 0; transform: translateY(0.45em); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 60rem) {
    .bento { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .cell--lead { grid-row: span 1; }
}
@media (max-width: 40rem) {
    .hero { padding-block: var(--space-2xl) var(--space-xl); }
    .hero__title { font-size: clamp(2.125rem, 10vw, 3rem); }
    .hero__lede { font-size: var(--text-md); }
    .bento { grid-template-columns: minmax(0, 1fr); gap: var(--space-xs); margin-bottom: var(--space-2xl); }
    .cell--lead, .cell--wide { grid-column: span 1; }
    .cell { padding: var(--space-md); gap: var(--space-md); }
    .foot__end { margin-left: 0; }
}
@media (prefers-reduced-motion: reduce) {
    .eyebrow, .hero__lede, .word, .cell, .foot { animation: none; }
    *, *::before, *::after { transition-duration: 150ms !important; }
}
</style>
</head>
<body>
<header class="shell nav">
<a href="{{ url('/') }}" class="nav__brand">Ruang Kerja</a>
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema terang atau gelap" aria-pressed="false">
<span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true"><flux:icon.moon variant="outline" /></span>
<span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true"><flux:icon.sun variant="outline" /></span>
</button>
</header>
<main class="shell">
<section class="hero" aria-labelledby="hero-title">
<p class="eyebrow">Satu tempat untuk pekerjaan harian</p>
<h1 id="hero-title" class="hero__title">
@foreach (preg_split('/\s+/', 'Semua catatan, satu alur yang rapi.') as $word)
<span class="word">{{ $word }}</span>
@endforeach
</h1>
<p class="hero__lede">Rapikan data masuk, pantau progres, lalu tutup pekerjaan di halaman yang sama — tanpa berpindah aplikasi atau menyalin ulang.</p>
</section>
<section class="bento" aria-label="Isi ruang kerja">
<article class="cell cell--lead">
<div>
<p class="cell__label">Alur utama</p>
<h2 class="cell__title">Dari data masuk sampai selesai.</h2>
<p class="cell__copy">Setiap langkah berada di satu jalur yang bisa ditelusuri, jadi tidak ada versi cerita yang berbeda-beda.</p>
</div>
<div class="readout">
<flux:badge size="sm" color="blue">tercatat</flux:badge>
<flux:badge size="sm" color="zinc">berjalan</flux:badge>
<flux:badge size="sm" color="zinc">selesai</flux:badge>
</div>
</article>
<article class="cell">
<div>
<p class="cell__label">Jejak</p>
<h2 class="cell__title">Perubahan tercatat</h2>
<p class="cell__copy">Siapa mengubah apa, dan kapan, tersimpan rapi.</p>
</div>
</article>
<article class="cell">
<div>
<p class="cell__label">Progres</p>
<h2 class="cell__title">Status terbaca</h2>
<p class="cell__copy">Tahu posisi pekerjaan tanpa perlu bertanya.</p>
</div>
</article>
<article class="cell cell--wide">
<div>
<p class="cell__label">Keluaran</p>
<h2 class="cell__title">Ringkasan siap diunduh</h2>
<p class="cell__copy">Buat berkas PDF atau Excel langsung dari tampilan yang sedang dibuka.</p>
</div>
</article>
<article class="cell">
<div>
<p class="cell__label">Akses</p>
<h2 class="cell__title">Izin berlapis</h2>
<p class="cell__copy">Hak dibagi per peran, bukan dibagi rata.</p>
</div>
</article>
<article class="cell">
<div>
<p class="cell__label">Cadangan</p>
<h2 class="cell__title">Salinan berkala</h2>
<p class="cell__copy">Data dicadangkan rutin dan bisa dipulihkan.</p>
</div>
</article>
<article class="cell cell--wide cell--dark">
<div>
<p class="cell__label">Lapangan</p>
<h2 class="cell__title">Posisi kiriman terlihat</h2>
<p class="cell__copy">Lacak perjalanan dan tanggapan masuk, bahkan dari layar kecil.</p>
</div>
</article>
</section>
</main>
<footer class="shell foot">
<span>Ruang Kerja</span>
<span class="foot__sep" aria-hidden="true">·</span>
<span>Ruang kerja operasional</span>
<span class="foot__end">© {{ date('Y') }}</span>
</footer>
<script>
(() => {
    const toggles = document.querySelectorAll('[data-theme-toggle]');
    const isDark = () => document.documentElement.classList.contains('dark');
    const sync = () => toggles.forEach((button) => button.setAttribute('aria-pressed', String(isDark())));

    toggles.forEach((button) => {
        button.addEventListener('click', () => {
            const next = isDark() ? 'light' : 'dark';
            window.Flux.applyAppearance(next);
            try { window.localStorage.setItem('theme', next); } catch {}
            sync();
        });
    });

    sync();
})();
</script>
</body>
</html>
