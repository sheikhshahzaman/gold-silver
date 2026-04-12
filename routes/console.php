<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch prices every minute (works on shared hosting without queue worker).
// The 55-second lock TTL ensures a hanging run can't block the next minute's tick.
Schedule::command('prices:fetch')->everyMinute()->withoutOverlapping(55);
