<?php

use App\Models\ActivityLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan log aktivitas lama sesuai config activity-log.keep_days.
Schedule::call(function () {
    ActivityLog::where('created_at', '<', now()->subDays((int) config('activity-log.keep_days')))->delete();
})->daily()->description('Prune old activity logs');
