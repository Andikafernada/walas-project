<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppJob;
use App\Models\WaQueue;
use Illuminate\Console\Command;

class ProcessWaQueue extends Command
{
    protected $signature = 'wa:process {--limit=100 : Number of messages to process}';

    protected $description = 'Process pending WhatsApp queue';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $pending = WaQueue::where('status', WaQueue::STATUS_PENDING)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending messages found.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$pending->count()} pending messages...");

        foreach ($pending as $queue) {
            SendWhatsAppJob::dispatch($queue);
            $this->line("  - Queued: {$queue->recipient_name} ({$queue->phone})");
        }

        $this->info('All messages dispatched to queue.');

        return Command::SUCCESS;
    }
}
