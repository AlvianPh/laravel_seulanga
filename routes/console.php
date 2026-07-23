<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Jadwal Notifikasi & Tagihan
Schedule::command('notify:invoice-created')->monthlyOn(1, '07:00');
Schedule::command('notify:invoice-due-soon')->dailyAt('08:00');
Schedule::command('notify:invoice-overdue')->dailyAt('08:15');
Schedule::command('notify:contract-ending')->dailyAt('08:30');
