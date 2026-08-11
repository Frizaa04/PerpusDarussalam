<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;   

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('cards:update-expired')->daily();
Schedule::command('laporan:pengunjung-bulanan')
    ->monthlyOn(1, '00:00');
Schedule::command('borrowings:generate-late-fees')->dailyAt('00:00');