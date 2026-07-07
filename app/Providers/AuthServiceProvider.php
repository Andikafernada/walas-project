<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\CashBook;
use App\Models\ClassModel;
use App\Models\Journal;
use App\Models\OrganizationStructure;
use App\Models\Schedule;
use App\Models\SeatingChart;
use App\Models\Student;
use App\Models\Violation;
use App\Models\WaQueue;
use App\Models\ApiToken;
use App\Policies\AttendancePolicy;
use App\Policies\AttendanceSessionPolicy;
use App\Policies\CashBookPolicy;
use App\Policies\ClassPolicy;
use App\Policies\JournalPolicy;
use App\Policies\OrganizationStructurePolicy;
use App\Policies\SchedulePolicy;
use App\Policies\SeatingChartPolicy;
use App\Policies\StudentPolicy;
use App\Policies\ViolationPolicy;
use App\Policies\WaQueuePolicy;
use App\Policies\ApiTokenPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ClassModel::class => ClassPolicy::class,
        Student::class => StudentPolicy::class,
        AttendanceSession::class => AttendanceSessionPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Violation::class => ViolationPolicy::class,
        CashBook::class => CashBookPolicy::class,
        Schedule::class => SchedulePolicy::class,
        Journal::class => JournalPolicy::class,
        OrganizationStructure::class => OrganizationStructurePolicy::class,
        SeatingChart::class => SeatingChartPolicy::class,
        WaQueue::class => WaQueuePolicy::class,
        ApiToken::class => ApiTokenPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
