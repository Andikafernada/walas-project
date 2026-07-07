<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Expire attendance sessions every 5 minutes
Schedule::command('sessions:clean --days=90')->weekly();

// Process WhatsApp queue every minute
Schedule::command('wa:process --limit=50')->everyMinute();

// Auto send attendance link based on schedule (every minute, weekdays)
Schedule::command('walas:auto-attendance')->weekdays()->everyMinute();

// Generate daily attendance at 6 AM on weekdays
Schedule::command('scheduler:run --daily')->weekdays()->at('06:00');

// Expire old sessions at 16:00 every day
Schedule::job(new \App\Jobs\ExpireAttendanceSessionsJob())->dailyAt('16:00');
