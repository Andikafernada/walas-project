<?php

namespace App\Console\Commands;

use App\Jobs\ExpireAttendanceSessionsJob;
use App\Jobs\GenerateDailyAttendanceJob;
use App\Jobs\GenerateMonthlyReportJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessScheduledTasks extends Command
{
    protected $signature = 'scheduler:run
                            {--daily : Run daily attendance generation}
                            {--expire : Run attendance expiration}
                            {--monthly : Generate monthly reports for all users}
                            {--all : Run all scheduled tasks}';

    protected $description = 'Run scheduled tasks manually';

    public function handle(): int
    {
        $this->info('Starting scheduled tasks...');

        if ($this->option('all') || $this->option('daily')) {
            $this->runDailyAttendance();
        }

        if ($this->option('all') || $this->option('expire')) {
            $this->runExpiration();
        }

        if ($this->option('all') || $this->option('monthly')) {
            $this->runMonthlyReports();
        }

        $this->info('All scheduled tasks completed!');

        return Command::SUCCESS;
    }

    protected function runDailyAttendance(): void
    {
        $this->info('Generating daily attendance sessions...');

        GenerateDailyAttendanceJob::dispatchSync();

        $this->info('Daily attendance generation completed.');
    }

    protected function runExpiration(): void
    {
        $this->info('Expiring attendance sessions...');

        ExpireAttendanceSessionsJob::dispatchSync();

        $this->info('Attendance expiration completed.');
    }

    protected function runMonthlyReports(): void
    {
        $this->info('Generating monthly reports...');

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // Generate for previous month on the 1st
        if (Carbon::now()->day === 1) {
            $month = Carbon::now()->subMonth()->month;
            $year = Carbon::now()->subMonth()->year;
        }

        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            GenerateMonthlyReportJob::dispatch($user->id, $year, $month);
            $this->line("  - Queued report for: {$user->name}");
        }

        $this->info("Monthly reports queued for {$users->count()} users.");
    }
}
