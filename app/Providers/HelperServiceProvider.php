<?php

namespace App\Providers;

use App\Helpers\AttendanceHelper;
use App\Helpers\DateHelper;
use App\Helpers\StringHelper;
use App\Services\AttendanceService;
use App\Services\ExportService;
use App\Services\ReportService;
use App\Services\StudentService;
use App\Services\WhatsAppService;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services as singletons
        $this->app->singleton(WhatsAppService::class, function ($app) {
            return new WhatsAppService();
        });

        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService($app->make(WhatsAppService::class));
        });

        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService();
        });

        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });

        $this->app->singleton(ExportService::class, function ($app) {
            return new ExportService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register helpers
        $this->registerHelpers();
    }

    /**
     * Register helper functions.
     */
    protected function registerHelpers(): void
    {
        // These helpers are available via direct class usage
        // e.g., DateHelper::format()
    }
}
