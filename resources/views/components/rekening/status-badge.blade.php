@props(['status'])

@php
    $styles = [
        'aktif' => ['label' => 'Aktif', 'color' => 'green'],
        'bermasalah' => ['label' => 'Bermasalah', 'color' => 'yellow'],
        'nonaktif' => ['label' => 'Non-Aktif', 'color' => 'zinc'],
    ];

    $style = $styles[$status] ?? ['label' => ucfirst((string) $status), 'color' => 'zinc'];
@endphp

<flux:badge :color="$style['color']" size="sm">{{ $style['label'] }}</flux:badge>
