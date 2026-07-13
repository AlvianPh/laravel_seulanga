<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Jadwal tagihan
Schedule::command('invoices:generate')->monthlyOn(1, '07:00');
Schedule::command('invoices:check-overdue')->daily();
