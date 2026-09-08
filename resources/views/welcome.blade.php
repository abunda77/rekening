<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:500|space-grotesk:500,600,700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        /* Hallmark · pre-emit critique: P5 H5 E5 S5 R5 V5 */
        /* Hallmark · genre: atmospheric · macrostructure: Map / Diagram · theme: Midnight · enrichment: none · nav: N9 · footer: Ft2 · tone: misterius · audience: admin dan agen logistik · use: masuk ke ruang kerja · contrast: pass (40–41) · slop: pass (42–45) */
        @import url('/tokens.css');

        html,
        body {
            overflow-x: clip;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at 84% 18%, var(--color-bloom), transparent 28rem),
                radial-gradient(circle at 18% 84%, var(--color-bloom-deep), transparent 24rem),
                var(--color-paper);
            color: var(--color-ink);
            font-family: var(--font-body);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        .page {
            min-height: 100dvh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .nav-edge,
        .foot-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-md);
            padding-inline: clamp(var(--space-md), 4vw, var(--space-2xl));
        }

        .nav-edge {
            padding-block: var(--space-lg);
        }

        .nav-edge::after {
            width: var(--space-xs);
            height: var(--space-xs);
            background: var(--color-accent);
            content: '';
        }

        .wordmark {
            color: var(--color-ink);
            font-family: var(--font-display);
            font-size: var(--text-lg);
            font-weight: 700;
            letter-spacing: -0.04em;
            text-decoration: none;
            white-space: nowrap;
        }

        .access-link,
        .node__link {
            color: var(--color-ink);
            font-family: var(--font-mono);
            font-size: var(--text-xs);
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
        }

        .access-link {
            border-bottom: var(--rule-thin) solid var(--color-accent);
            padding-bottom: var(--space-2xs);
            transition: color var(--dur-short) var(--ease-out), transform var(--dur-micro) var(--ease-out);
        }

        .access-link:hover {
            color: var(--color-accent);
            transform: translateY(-1px);
        }

        .access-link:active {
            transform: translateY(1px);
        }

        .access-link:focus-visible,
        .node__link:focus-visible,
        .wordmark:focus-visible {
            outline: 3px solid var(--color-focus);
            outline-offset: 4px;
        }

        .map-layout {
            width: min(100% - (var(--space-md) * 2), 88rem);
            margin-inline: auto;
            padding-block: clamp(var(--space-xl), 8vw, var(--space-4xl));
        }

        .map-intro {
            display: grid;
            gap: var(--space-lg);
            padding-bottom: clamp(var(--space-xl), 7vw, var(--space-3xl));
        }

        .map-intro__title {
            max-width: 11ch;
            margin: 0;
            font-family: var(--font-display);
            font-size: var(--text-display);
            font-style: normal;
            font-weight: 600;
            letter-spacing: -0.07em;
            line-height: 0.9;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .map-intro__copy {
            max-width: 56ch;
            margin: 0;
            color: var(--color-ink-soft);
            font-size: var(--text-lg);
            line-height: 1.6;
        }

        .system-map {
            position: relative;
            display: grid;
            gap: var(--space-lg);
            padding: var(--space-lg) 0 var(--space-xl);
            border-top: var(--rule-thin) solid var(--color-rule);
            border-bottom: var(--rule-thin) solid var(--color-rule);
        }

        .node {
            position: relative;
            display: grid;
            gap: var(--space-sm);
            min-width: 0;
            padding: var(--space-lg);
            border: var(--rule-thin) solid var(--color-rule);
            background: var(--color-paper-raised);
        }

        .node::before {
            content: '';
            width: var(--space-xs);
            height: var(--space-xs);
            background: var(--color-accent);
        }

        .node__title {
            margin: 0;
            font-family: var(--font-display);
            font-size: var(--text-xl);
            font-style: normal;
            font-weight: 600;
            letter-spacing: -0.04em;
        }

        .node__copy {
            margin: 0;
            color: var(--color-ink-soft);
            font-size: var(--text-sm);
            line-height: 1.55;
        }

        .node__link {
            width: fit-content;
            color: var(--color-accent);
            border-bottom: var(--rule-thin) solid var(--color-accent);
            padding-bottom: var(--space-3xs);
        }

        .node__link:hover {
            color: var(--color-ink);
        }

        .node:hover {
            border-color: var(--color-accent);
        }

        .map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-md);
            padding-top: var(--space-md);
            color: var(--color-muted);
            font-family: var(--font-mono);
            font-size: var(--text-xs);
        }

        .map-legend__item {
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .map-legend__mark {
            width: var(--space-xs);
            height: var(--space-xs);
            background: var(--color-accent);
        }

        .map-legend__line {
            width: var(--space-xl);
            height: var(--rule-thin);
            background: var(--color-rule);
        }

        .foot-line {
            border-top: var(--rule-thin) solid var(--color-rule);
            padding-block: var(--space-md);
            color: var(--color-muted);
            font-family: var(--font-mono);
            font-size: var(--text-xs);
        }

        .foot-line p {
            margin: 0;
        }

        @media (min-width: 40rem) {
            .map-intro {
                grid-template-columns: minmax(0, 1.25fr) minmax(0, 0.75fr);
                align-items: end;
            }

            .map-intro__copy {
                padding-bottom: var(--space-sm);
            }

            .system-map {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);
                gap: var(--space-2xl);
                padding-block: var(--space-2xl);
            }

        .system-map::before,
        .system-map::after {
            position: absolute;
            top: 50%;
            width: var(--space-2xl);
            height: var(--rule-thin);
            background: linear-gradient(90deg, var(--color-rule), var(--color-accent-soft), var(--color-rule));
            content: '';
        }

            .system-map::before {
                left: calc(33.333% - var(--space-xl));
            }

            .system-map::after {
                right: calc(33.333% - var(--space-xl));
            }

            .node {
                min-height: 15rem;
            }
        }

        @keyframes reveal-fade {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .map-intro,
        .system-map,
        .map-legend {
            animation: reveal-fade 520ms var(--ease-out) both;
        }

        .system-map {
            animation-delay: 120ms;
        }

        .map-legend {
            animation-delay: 240ms;
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 150ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 150ms !important;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="nav-edge">
            <a href="{{ url('/') }}" class="wordmark">{{ config('app.name', 'Rekening') }}</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/agent/dashboard') }}" class="access-link">Buka dashboard →</a>
                @else
                    <a href="{{ url('/agent/login') }}" class="access-link">Masuk agen →</a>
                @endauth
            @endif
        </header>

        <main class="map-layout">
            <section class="map-intro" aria-labelledby="map-title">
                <h1 id="map-title" class="map-intro__title">Satu alur. Setiap langkah tercatat.</h1>
                <p class="map-intro__copy">Ruang kerja untuk menghubungkan data rekening, jaringan agen, dan proses pengiriman dalam satu alur operasional yang dapat ditelusuri.</p>
            </section>

            <section class="system-map" aria-label="Peta alur operasional">
                <article class="node">
                    <h2 class="node__title">Rekening</h2>
                    <p class="node__copy">Data penting tersusun sebagai titik awal operasi, dengan akses yang terjaga.</p>
                </article>

                <article class="node">
                    <h2 class="node__title">Agen</h2>
                    <p class="node__copy">Jaringan kerja bergerak dari informasi yang sama, tanpa memutus konteks proses.</p>
                    <a href="{{ url('/agent/login') }}" class="node__link">Masuk sebagai agen →</a>
                </article>

                <article class="node">
                    <h2 class="node__title">Pengiriman</h2>
                    <p class="node__copy">Setiap pengiriman tetap terhubung ke sumber informasi yang membentuknya.</p>
                </article>
            </section>

            <div class="map-legend" aria-label="Keterangan peta">
                <span class="map-legend__item"><span class="map-legend__mark" aria-hidden="true"></span>Titik kerja aktif</span>
                <span class="map-legend__item"><span class="map-legend__line" aria-hidden="true"></span>Alur informasi</span>
            </div>
        </main>

        <footer class="foot-line">
            <p>{{ config('app.name', 'Rekening') }} · Sistem operasional terpadu</p>
            <p>© {{ date('Y') }}</p>
        </footer>
    </div>
</body>
</html>
