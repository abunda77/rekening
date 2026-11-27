<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    |
    | Setiap request web dan API dicatat ke tabel activity_logs dan ditampilkan
    | pada menu Activity di dashboard. Matikan lewat ACTIVITY_LOG_ENABLED bila
    | pencatatan tidak diperlukan (misalnya saat import data besar).
    |
    */

    'enabled' => env('ACTIVITY_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | Umur log dalam hari sebelum dibersihkan oleh perintah terjadwal.
    |
    */

    'keep_days' => env('ACTIVITY_LOG_KEEP_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Ignored Requests
    |--------------------------------------------------------------------------
    |
    | Pola path yang tidak perlu dicatat karena hanya berupa plumbing UI,
    | health check, atau aset statis.
    |
    */

    'ignore_paths' => [
        'up',
        'livewire/*',
        'rekening/activities*',
        'build/*',
        'storage/*',
        'favicon.ico',
    ],

    'ignore_methods' => ['HEAD', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | Redacted Input
    |--------------------------------------------------------------------------
    |
    | Key input yang mengandung salah satu potongan kata di bawah ini tidak
    | pernah disimpan nilainya, hanya ditandai [redacted].
    |
    */

    'redacted_key_fragments' => [
        'password',
        'token',
        'secret',
        'cvv',
        'card_number',
        'api_key',
        'otp',
        'recovery_code',
        'authorization',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payload Limit
    |--------------------------------------------------------------------------
    |
    | Panjang maksimum payload (dalam byte) yang disimpan. Payload yang lebih
    | besar hanya disimpan daftar key-nya saja.
    |
    */

    'payload_max_length' => 16000,

];
