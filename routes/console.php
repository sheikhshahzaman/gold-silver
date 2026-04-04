<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch prices every 5 minutes (works on shared hosting without queue worker)
Schedule::command('prices:fetch')->everyFiveMinutes()->withoutOverlapping();
