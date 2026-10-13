<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// Parte 3: mails automáticos de facturación (se corren todos los días;
// cada comando decide si ese día le toca actuar)
// ============================================================
Schedule::command('facturas:recordatorio')->dailyAt('08:00');
Schedule::command('facturas:deuda')->dailyAt('08:00');
