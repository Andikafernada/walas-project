<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use Illuminate\Console\Command;

class CleanOldSessions extends Command
{
    protected $signature = 'sessions:clean
                            {--days=30 : Delete sessions older than this many days}
                            {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Clean old attendance sessions and related data';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $cutoffDate = now()->subDays($days);

        $sessions = AttendanceSession::where('created_at', '<', $cutoffDate)->get();

        if ($sessions->isEmpty()) {
            $this->info('No old sessions to clean.');
            return Command::SUCCESS;
        }

        $this->info("Found {$sessions->count()} sessions older than {$days} days.");

        if ($dryRun) {
            $this->warn('DRY RUN - No data will be deleted.');
            foreach ($sessions->take(10) as $session) {
                $this->line("  - Session #{$session->id}: {$session->date->format('Y-m-d')} ({$session->classModel->name})");
            }
            if ($sessions->count() > 10) {
                $this->line("  ... and " . ($sessions->count() - 10) . " more");
            }
            return Command::SUCCESS;
        }

        if (!$this->confirm("Delete {$sessions->count()} sessions?")) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $deleted = 0;
        foreach ($sessions as $session) {
            $session->attendances()->delete();
            $session->delete();
            $deleted++;
        }

        $this->info("Deleted {$deleted} sessions and their attendances.");

        return Command::SUCCESS;
    }
}
