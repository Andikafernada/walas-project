<?php

namespace App\Jobs;

use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireAttendanceSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function handle(): void
    {
        $count = AttendanceSession::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        Log::info('Expired attendance sessions', ['count' => $count]);
    }
}
