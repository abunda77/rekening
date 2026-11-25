@php
    /*
     * Shared list PDF template.
     *
     * Each row is rendered as a single text line instead of a table with one cell per
     * column: dompdf costs roughly one style/reflow pass per cell, so wide tables blow up
     * memory and time on large datasets. Columns stay aligned because the font is
     * monospaced and every cell is padded to the widest value of its column.
     */
    $columnCount = count($headers);
    $widths = [];

    for ($column = 0; $column < $columnCount; $column++) {
        $width = mb_strwidth($headers[$column]);

        foreach ($rows as $row) {
            $width = max($width, mb_strwidth((string) ($row[$column] ?? '')));
        }

        $widths[$column] = $width;
    }

    $pad = function (string $value, int $width): string {
        $current = mb_strwidth($value);

        return $current >= $width ? $value : $value.str_repeat(' ', $width - $current);
    };

    $line = fn (array $cells): string => collect($cells)
        ->map(fn ($cell, $column) => $pad((string) $cell, $widths[$column]))
        ->implode('  ');

    // A4 landscape fits ~1720 / font-size characters per line for Courier, so the font
    // shrinks a little on very wide tables instead of pushing columns out of alignment.
    $totalWidth = array_sum($widths) + 2 * max(0, $columnCount - 1);
    $fontSize = number_format(min(8, max(5, 1720 / max($totalWidth, 1))), 1);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Courier', monospace; font-size: {{ $fontSize }}px; color: #111827; }
        h2 { font-size: 13px; margin: 0 0 4px; }
        .meta { margin: 0 0 8px; color: #4b5563; }
        /* pre-wrap keeps very long values on the page instead of clipping them. */
        .line { white-space: pre-wrap; padding: 1px 0; page-break-inside: avoid; }
        .head { border-bottom: 1px solid #111827; font-weight: bold; }
        .row { border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>

    <div class="meta">{{ implode(' · ', $meta) }}</div>

    <div class="line head">{{ $line($headers) }}</div>

    @foreach($rows as $row)
        <div class="line row">{{ $line($row) }}</div>
    @endforeach
</body>
</html>
