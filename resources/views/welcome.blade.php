<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ config('app.name', 'Laravel') }}</title>
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=dm-serif-display:400|inter:400,500,600,700|jetbrains-mono:500&display=swap" rel="stylesheet">
@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
@vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
<style>
/* Hallmark · macrostructure: Map / Diagram · tone: after-hours operational · anchor hue: amber · theme: Midnight · nav: N13 · footer: Ft5 · knobs: map=three-lane, nodes=5, edges=flow-line */
/* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
@import url('/tokens.css');

html, body { overflow-x: clip; }
body { margin: 0; background: var(--color-paper); color: var(--color-ink); font-family: var(--font-body); }
*, *::before, *::after { box-sizing: border-box; }
a { color: inherit; }
button, input { font: inherit; }
button { color: inherit; }
.page { min-height: 100dvh; }
.nav { width: min(100% - (var(--space-md) * 2), 82rem); margin-inline: auto; padding-block: var(--space-lg); }
.nav__inner { display: grid; grid-template-columns: auto minmax(12rem, 22rem) auto; align-items: center; gap: var(--space-lg); padding: var(--space-xs); border: var(--rule-thin) solid var(--color-rule); border-radius: 999px; background: color-mix(in oklch, var(--color-paper-raised) 88%, transparent); }
.nav__brand { padding-inline: var(--space-sm); font-family: var(--font-display); font-size: var(--text-xl); text-decoration: none; white-space: nowrap; }
.nav__right { display: flex; align-items: center; justify-content: end; gap: var(--space-md); padding-inline: var(--space-sm); font-family: var(--font-mono); font-size: var(--text-xs); }
.nav__link { text-decoration: none; white-space: nowrap; }
.nav__link:hover { color: var(--color-accent); }
.searchpill { display: flex; min-width: 0; align-items: center; justify-content: space-between; gap: var(--space-xs); min-height: 2.5rem; padding: var(--space-xs) var(--space-xs) var(--space-xs) var(--space-sm); border: var(--rule-thin) solid var(--color-rule); border-radius: 999px; background: var(--color-paper); color: var(--color-muted); cursor: pointer; transition: border-color var(--dur-short) var(--ease-out), color var(--dur-short) var(--ease-out), transform var(--dur-micro) var(--ease-out); }
.searchpill:hover { border-color: var(--color-accent); color: var(--color-ink); }
.searchpill:active { transform: translateY(1px); }
.searchpill:focus-visible, .cmdk__close:focus-visible, .cmdk__item:focus-visible, .inline-cta:focus-visible { outline: 3px solid var(--color-focus); outline-offset: 4px; }
.searchpill__label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.searchpill__kbd, .cmdk kbd { padding: var(--space-3xs) var(--space-xs); border: var(--rule-thin) solid var(--color-rule); border-radius: var(--radius-sm); color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); white-space: nowrap; }
.hero { width: min(100% - (var(--space-md) * 2), 82rem); margin-inline: auto; padding-block: clamp(var(--space-3xl), 11vw, 10rem) var(--space-2xl); }
.eyebrow, .node__meta, .legend, .map-caption { color: var(--color-accent); font-family: var(--font-mono); font-size: var(--text-xs); letter-spacing: 0.04em; text-transform: uppercase; }
.hero__title { max-width: 10ch; margin: var(--space-sm) 0 var(--space-lg); font-family: var(--font-display); font-size: var(--text-display); font-style: normal; font-weight: 400; letter-spacing: -0.055em; line-height: 0.87; min-width: 0; overflow-wrap: anywhere; }
.hero__copy { max-width: 46ch; margin: 0; color: var(--color-ink-soft); font-size: var(--text-lg); line-height: 1.55; }
.map-section { width: min(100% - (var(--space-md) * 2), 82rem); margin-inline: auto; padding-block: var(--space-2xl) var(--space-3xl); }
.map-frame { position: relative; min-height: 32rem; padding: clamp(var(--space-lg), 5vw, var(--space-2xl)); border: var(--rule-thin) solid var(--color-rule); background: linear-gradient(135deg, var(--color-paper-raised), var(--color-paper-deep)); overflow: hidden; }
.map-frame::before, .map-frame::after { position: absolute; content: ''; pointer-events: none; }
.map-frame::before { inset: var(--space-lg); border: var(--rule-thin) solid var(--color-rule); opacity: 0.55; }
.map-frame::after { top: 50%; left: 0; width: 100%; border-top: var(--rule-thin) dashed var(--color-rule); opacity: 0.7; }
.map-caption { position: relative; z-index: 1; display: flex; justify-content: space-between; gap: var(--space-md); margin-bottom: var(--space-2xl); }
.map-caption span:last-child { color: var(--color-muted); text-align: right; }
.map { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); grid-template-rows: repeat(3, minmax(5rem, auto)); align-items: center; gap: var(--space-xl) var(--space-lg); }
.map__edge { position: absolute; z-index: -1; height: var(--rule-thin); background: var(--color-accent-soft); transform-origin: left center; }
.map__edge--one { top: 28%; left: 18%; width: 30%; transform: rotate(18deg); }
.map__edge--two { top: 50%; left: 46%; width: 29%; transform: rotate(-18deg); }
.map__edge--three { top: 68%; left: 20%; width: 55%; transform: rotate(0deg); }
.node { display: flex; min-height: 7.5rem; flex-direction: column; justify-content: space-between; gap: var(--space-sm); padding: var(--space-md); border: var(--rule-thin) solid var(--color-rule); background: color-mix(in oklch, var(--color-paper) 72%, transparent); transition: border-color var(--dur-short) var(--ease-out), transform var(--dur-short) var(--ease-out), background var(--dur-short) var(--ease-out); }
.node:hover { border-color: var(--color-accent); background: var(--color-accent-soft); transform: translateY(-3px); }
.node:focus-within { border-color: var(--color-focus); }
.node--account { grid-column: 1; grid-row: 1; }
.node--agent { grid-column: 3; grid-row: 2; }
.node--shipment { grid-column: 1; grid-row: 3; }
.node--trace { grid-column: 2; grid-row: 2; border-color: var(--color-accent); }
.node--result { grid-column: 3; grid-row: 3; }
.node__title { margin: 0; font-family: var(--font-display); font-size: var(--text-xl); font-weight: 400; line-height: 1; }
.node__copy { margin: 0; color: var(--color-ink-soft); font-size: var(--text-sm); line-height: 1.5; }
.node__link { align-self: start; color: var(--color-accent); font-family: var(--font-mono); font-size: var(--text-xs); text-decoration: none; white-space: nowrap; }
.node__link:hover { color: var(--color-ink); }
.legend { display: flex; flex-wrap: wrap; gap: var(--space-md); margin-top: var(--space-xl); color: var(--color-muted); }
.legend__item { display: inline-flex; align-items: center; gap: var(--space-xs); }
.legend__dot { width: 0.5rem; height: 0.5rem; border-radius: 50%; background: var(--color-accent); }
.legend__dot--muted { background: var(--color-rule); }
.inline-cta { display: inline-flex; margin-top: var(--space-2xl); padding: var(--space-sm) var(--space-md); border: var(--rule-thin) solid var(--color-accent); color: var(--color-accent); font-family: var(--font-mono); font-size: var(--text-xs); text-decoration: none; transition: background var(--dur-short) var(--ease-out), color var(--dur-short) var(--ease-out), transform var(--dur-micro) var(--ease-out); }
.inline-cta:hover { background: var(--color-accent); color: var(--color-paper); transform: translateY(-2px); }
.inline-cta:active { transform: translateY(1px); }
.foot-stmt { width: min(100% - (var(--space-md) * 2), 82rem); margin-inline: auto; padding-block: var(--space-2xl) var(--space-xl); border-top: var(--rule-thin) solid var(--color-rule); }
.foot-stmt__line { max-width: 28ch; margin: 0 0 var(--space-xl); font-family: var(--font-display); font-size: clamp(1.75rem, 5vw, 3.25rem); font-style: normal; line-height: 1; letter-spacing: -0.02em; }
.foot-stmt__meta { display: flex; justify-content: space-between; gap: var(--space-md); padding-top: var(--space-sm); color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); }
.cmdk { position: fixed; inset: 0; z-index: 10; display: grid; place-items: start center; padding: 14vh var(--space-md); background: color-mix(in oklch, var(--color-paper-deep) 82%, transparent); opacity: 0; pointer-events: none; transition: opacity var(--dur-short) var(--ease-out); }
.cmdk.is-open { opacity: 1; pointer-events: auto; }
.cmdk__panel { width: min(36rem, 100%); border: var(--rule-thin) solid var(--color-rule); background: var(--color-paper-raised); transform: translateY(-8px); transition: transform var(--dur-short) var(--ease-out); }
.cmdk.is-open .cmdk__panel { transform: translateY(0); }
.cmdk__head { display: flex; align-items: center; justify-content: space-between; gap: var(--space-md); padding: var(--space-md); border-bottom: var(--rule-thin) solid var(--color-rule); }
.cmdk__input { width: 100%; min-width: 0; border: 0; outline: 0; background: transparent; color: var(--color-ink); }
.cmdk__input::placeholder { color: var(--color-muted); }
.cmdk__close { padding: var(--space-xs); border: 0; background: transparent; color: var(--color-muted); cursor: pointer; }
.cmdk__list { display: grid; gap: var(--space-2xs); padding: var(--space-sm); }
.cmdk__group { margin: var(--space-xs) var(--space-sm); color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); text-transform: uppercase; }
.cmdk__item { display: flex; align-items: center; justify-content: space-between; gap: var(--space-md); padding: var(--space-sm); border: 0; background: transparent; text-align: left; cursor: pointer; }
 .cmdk__item:hover, .cmdk__item.is-active { background: var(--color-accent-soft); color: var(--color-accent); }
 .cmdk__foot { display: flex; flex-wrap: wrap; gap: var(--space-md); padding: var(--space-sm) var(--space-md); border-top: var(--rule-thin) solid var(--color-rule); color: var(--color-muted); font-family: var(--font-mono); font-size: var(--text-xs); }
 .nav, .hero, .map-section, .foot-stmt { animation: rise-in var(--dur-long) var(--ease-out) both; }
 .hero { animation-delay: 80ms; }
 .map-section { animation-delay: 160ms; }
 .foot-stmt { animation-delay: 240ms; }
 .node { animation: node-in var(--dur-long) var(--ease-out) both; }
 .node--account { animation-delay: 300ms; }
 .node--agent { animation-delay: 380ms; }
 .node--trace { animation-delay: 460ms; }
 .node--shipment { animation-delay: 540ms; }
 .node--result { animation-delay: 620ms; }
 .map__edge { animation: edge-breathe var(--dur-long) var(--ease-out) both; }
 .map__edge--one { animation-delay: 420ms; }
 .map__edge--two { animation-delay: 500ms; }
 .map__edge--three { animation-delay: 580ms; }
 @keyframes rise-in { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
 @keyframes node-in { from { opacity: 0; transform: translateY(10px) scale(0.985); } to { opacity: 1; transform: translateY(0) scale(1); } }
 @keyframes edge-breathe { from { opacity: 0; } to { opacity: 1; } }
 @media (max-width: 50rem) {
.nav__inner { grid-template-columns: auto 1fr auto; }
.nav__right .nav__link { display: none; }
.map { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-template-rows: none; }
.node--account, .node--agent, .node--shipment, .node--trace, .node--result { grid-column: auto; grid-row: auto; }
.map__edge { display: none; }
}
@media (max-width: 40rem) {
.nav { width: min(100% - var(--space-md), 82rem); }
.nav__inner { grid-template-columns: auto 1fr; }
.nav__brand { font-size: var(--text-lg); }
.searchpill { justify-self: end; }
.searchpill__label { display: none; }
.hero, .map-section, .foot-stmt { width: min(100% - var(--space-md), 82rem); }
.hero { padding-block: var(--space-3xl) var(--space-2xl); }
.hero__title { font-size: clamp(3.5rem, 19vw, 6.5rem); }
.map-frame { min-height: 0; padding: var(--space-md); }
.map-frame::before { inset: var(--space-sm); }
.map-caption { flex-direction: column; gap: var(--space-xs); margin-bottom: var(--space-xl); }
.map-caption span:last-child { text-align: left; }
.map { grid-template-columns: 1fr; gap: var(--space-sm); }
.node { min-height: 0; }
.foot-stmt__meta { align-items: flex-start; flex-direction: column; }
}
@media (prefers-reduced-motion: reduce) {
*, *::before, *::after { animation-duration: 150ms !important; animation-delay: 0ms !important; transition-duration: 150ms !important; }
}
</style>
</head>
<body>
<div class="page">
<header class="nav" id="top">
<div class="nav__inner">
<a href="{{ url('/') }}" class="nav__brand">{{ config('app.name', 'Rekening') }}</a>
<button class="searchpill" id="searchpill" type="button" aria-label="Buka pencarian" aria-controls="cmdk" aria-expanded="false"><span class="searchpill__label">Cari ruang kerja…</span><span class="searchpill__kbd"><kbd>⌘</kbd><kbd>K</kbd></span></button>
<nav class="nav__right" aria-label="Navigasi utama">
<a href="#peta" class="nav__link">Peta alur</a>
@if (Route::has('login'))
@auth
<a href="{{ url('/agent/dashboard') }}" class="nav__link">Dashboard →</a>
@else
<a href="{{ url('/agent/login') }}" class="nav__link">Masuk agen →</a>
@endauth
@endif
</nav>
</div>
</header>
<main>
<section class="hero" aria-labelledby="hero-title">
<div class="eyebrow">Tiga jalur · satu konteks</div>
<h1 id="hero-title" class="hero__title">Semua bergerak dari titik yang sama.</h1>
<p class="hero__copy">Ruang kerja untuk menghubungkan data rekening, jaringan agen, dan proses pengiriman dalam satu alur operasional yang dapat ditelusuri.</p>
</section>
<section class="map-section" id="peta" aria-labelledby="map-title">
<div class="map-frame">
<div class="map-caption"><span id="map-title">Peta operasional</span><span>Data → jaringan → proses → hasil</span></div>
<div class="map" aria-label="Diagram hubungan rekening, agen, pengiriman, dan hasil">
<span class="map__edge map__edge--one" aria-hidden="true"></span><span class="map__edge map__edge--two" aria-hidden="true"></span><span class="map__edge map__edge--three" aria-hidden="true"></span>
<article class="node node--account"><div class="node__meta">01 / Data</div><h2 class="node__title">Rekening</h2><p class="node__copy">Titik awal informasi penting dengan akses yang terjaga.</p></article>
<article class="node node--agent"><div class="node__meta">02 / Jaringan</div><h2 class="node__title">Agen</h2><p class="node__copy">Konteks yang sama dibawa ke lapangan tanpa terputus.</p><a href="{{ url('/agent/login') }}" class="node__link">Buka ruang agen →</a></article>
<article class="node node--trace"><div class="node__meta">03 / Hub</div><h2 class="node__title">Konteks</h2><p class="node__copy">Informasi bertemu sebelum tindakan bergerak lebih jauh.</p></article>
<article class="node node--shipment"><div class="node__meta">04 / Proses</div><h2 class="node__title">Pengiriman</h2><p class="node__copy">Setiap langkah tetap terhubung ke sumber yang membentuknya.</p></article>
<article class="node node--result"><div class="node__meta">05 / Jejak</div><h2 class="node__title">Terlihat</h2><p class="node__copy">Status dan perubahan mudah ditelusuri sampai selesai.</p></article>
</div>
<div class="legend"><span class="legend__item"><i class="legend__dot"></i> jalur aktif</span><span class="legend__item"><i class="legend__dot legend__dot--muted"></i> titik informasi</span></div>
</div>
<a href="{{ url('/agent/login') }}" class="inline-cta">Masuk sebagai agen →</a>
</section>
</main>
<footer class="foot-stmt"><p class="foot-stmt__line">Yang perlu bergerak, tetap terlihat.</p><div class="foot-stmt__meta"><span>{{ config('app.name', 'Rekening') }} · Sistem operasional terpadu</span><span>© {{ date('Y') }}</span></div></footer>
</div>
<div class="cmdk" id="cmdk" aria-hidden="true">
<div class="cmdk__panel" role="dialog" aria-modal="true" aria-labelledby="cmdk-title">
<div class="cmdk__head"><input class="cmdk__input" id="cmdk-input" type="search" placeholder="Cari ruang kerja…" aria-label="Cari ruang kerja"><button class="cmdk__close" id="cmdk-close" type="button" aria-label="Tutup pencarian">Esc</button></div>
<div class="cmdk__list"><p class="cmdk__group" id="cmdk-title">Tujuan cepat</p><a class="cmdk__item" href="#peta">Peta alur <span>↵</span></a><a class="cmdk__item" href="{{ url('/agent/login') }}">Masuk agen <span>↵</span></a></div>
<div class="cmdk__foot"><span><kbd>↑</kbd><kbd>↓</kbd> navigasi</span><span><kbd>Esc</kbd> tutup</span></div>
</div>
</div>
<script>
const searchpill = document.getElementById('searchpill');
const cmdk = document.getElementById('cmdk');
const cmdkInput = document.getElementById('cmdk-input');
const cmdkClose = document.getElementById('cmdk-close');
let lastFocused = null;
const setCommandMenu = (open) => {
    cmdk.classList.toggle('is-open', open);
    cmdk.setAttribute('aria-hidden', String(!open));
    searchpill.setAttribute('aria-expanded', String(open));
    if (open) {
        lastFocused = document.activeElement;
        window.setTimeout(() => cmdkInput.focus(), 0);
    } else if (lastFocused) {
        lastFocused.focus();
    }
};
searchpill.addEventListener('click', () => setCommandMenu(true));
cmdkClose.addEventListener('click', () => setCommandMenu(false));
cmdk.addEventListener('click', (event) => { if (event.target === cmdk) setCommandMenu(false); });
document.addEventListener('keydown', (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); setCommandMenu(!cmdk.classList.contains('is-open')); }
    if (event.key === 'Escape' && cmdk.classList.contains('is-open')) setCommandMenu(false);
});
</script>
</body>
</html>
