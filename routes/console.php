<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Corre a diario: así también reciben su regalo las clientas que registran su cumpleaños a mitad de mes.
Schedule::command('correos:cumpleanos')
    ->dailyAt('10:00')
    ->withoutOverlapping();

Schedule::command('correos:gracias-visita')
    ->dailyAt('11:00')
    ->withoutOverlapping();
