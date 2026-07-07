<?php

namespace App\Jobs;

use App\Models\AttendanceSession;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateMonthlyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public int $userId,
        public int $year,
        public int $month
    ) {}

    public function handle(ReportService $reportService): void
    {
        $user = \App\Models\User::find($this->userId);

        if (!$user) {
            Log::warning('User not found for monthly report', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $report = $reportService->getMonthlySummary($this->year, $this->month);

        // Create or update report record
        $reportRecord = \App\Models\MonthlyReport::updateOrCreate(
            [
                'user_id' => $this->userId,
                'year' => $this->year,
                'month' => $this->month,
            ],
            [
                'data' => $report,
                'generated_at' => now(),
            ]
        );

        // Queue notifications if report is ready
        if ($reportRecord) {
            Log::info('Monthly report generated', [
                'user_id' => $this->userId,
                'year' => $this->year,
                'month' => $this->month,
            ]);
        }
    }
}
