<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily database backup at 01:30
Schedule::command('backup:run --only-db')
    ->daily()
    ->at('01:30')
    ->name('daily-db-backup')
    ->withoutOverlapping()
    ->onOneServer();

// Weekly full backup (Friday at 02:00)
Schedule::command('backup:run')
    ->fridays()
    ->at('02:00')
    ->name('weekly-full-backup')
    ->withoutOverlapping()
    ->onOneServer();

// Weekly cleanup (Friday at 02:30)
Schedule::command('backup:clean')
    ->fridays()
    ->at('02:30')
    ->name('weekly-backup-cleanup')
    ->withoutOverlapping()
    ->onOneServer();
