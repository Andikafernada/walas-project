<?php

namespace Tests\Unit\Services;

use App\Helpers\DateHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DateHelperTest extends TestCase
{
    public function test_format_date_long(): void
    {
        $date = '2024-01-15';
        $formatted = DateHelper::formatDate($date, 'long');

        $this->assertStringContainsString('15', $formatted);
        $this->assertStringContainsString('Januari', $formatted);
        $this->assertStringContainsString('2024', $formatted);
    }

    public function test_format_date_short(): void
    {
        $date = '2024-01-15';
        $formatted = DateHelper::formatDate($date, 'short');

        $this->assertStringContainsString('15', $formatted);
        $this->assertStringContainsString('Jan', $formatted);
    }

    public function test_format_date_num(): void
    {
        $date = '2024-01-15';
        $formatted = DateHelper::formatDate($date, 'num');

        $this->assertEquals('15/01/2024', $formatted);
    }

    public function test_format_time(): void
    {
        $formatted = DateHelper::formatTime('2024-01-15 14:30:00');

        $this->assertEquals('14:30', $formatted);
    }

    public function test_format_time_with_seconds(): void
    {
        $formatted = DateHelper::formatTime('2024-01-15 14:30:45', true);

        $this->assertEquals('14:30:45', $formatted);
    }

    public function test_get_day_name(): void
    {
        $monday = '2024-01-15'; // Monday
        $sunday = '2024-01-21'; // Sunday

        $this->assertEquals('Senin', DateHelper::getDayName($monday));
        $this->assertEquals('Minggu', DateHelper::getDayName($sunday));
    }

    public function test_get_month_name(): void
    {
        $this->assertEquals('Januari', DateHelper::getMonthName(1));
        $this->assertEquals('Februari', DateHelper::getMonthName(2));
        $this->assertEquals('Desember', DateHelper::getMonthName(12));
    }

    public function test_is_today(): void
    {
        $this->assertTrue(DateHelper::isToday(now()));
        $this->assertFalse(DateHelper::isToday(now()->subDay()));
    }

    public function test_school_year_format(): void
    {
        $this->assertEquals('2024/25', DateHelper::schoolYear(2024, 2025));
        $this->assertEquals('2024/25', DateHelper::schoolYear(2024));
    }

    public function test_relative_time(): void
    {
        $justNow = now()->subSeconds(30);
        $minutes = now()->subMinutes(5);
        $hours = now()->subHours(2);
        $days = now()->subDays(3);

        $this->assertEquals('baru saja', DateHelper::relativeTime($justNow));
        $this->assertEquals('5 menit yang lalu', DateHelper::relativeTime($minutes));
        $this->assertEquals('2 jam yang lalu', DateHelper::relativeTime($hours));
        $this->assertEquals('3 hari yang lalu', DateHelper::relativeTime($days));
    }
}
