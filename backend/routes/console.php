<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:backup', function () {
    $source = database_path('database.sqlite');

    if (! file_exists($source)) {
        $this->error('SQLite database file not found.');
        return;
    }

    $targetDir = storage_path('app/backups');
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $target = $targetDir . '/database-' . now()->format('Ymd-His') . '.sqlite';
    copy($source, $target);

    $this->info('Backup created: ' . $target);
})->purpose('Backup SQLite database to storage/app/backups');

Schedule::command('db:backup')->dailyAt('02:00');
Schedule::command('report:weekly')->weeklyOn(1, '08:00');
