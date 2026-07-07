<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format date for Indonesian locale.
     */
    public static function formatDate(string|Carbon $date, string $format = 'long'): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return match($format) {
            'long' => $date->locale('id')->translatedFormat('d F Y'),
            'short' => $date->locale('id')->translatedFormat('d M Y'),
            'day' => $date->locale('id')->dayName,
            'month' => $date->locale('id')->monthName,
            'day_short' => $date->locale('id')->format('D'),
            'num' => $date->format('d/m/Y'),
            'iso' => $date->format('Y-m-d'),
            default => $date->format($format),
        };
    }

    /**
     * Format time.
     */
    public static function formatTime(string|Carbon $date, bool $withSeconds = false): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $date->format($withSeconds ? 'H:i:s' : 'H:i');
    }

    /**
     * Format datetime.
     */
    public static function formatDateTime(string|Carbon $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $date->locale('id')->translatedFormat('d F Y, H:i');
    }

    /**
     * Get relative time in Indonesian.
     */
    public static function relativeTime(string|Carbon $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $now = Carbon::now();
        $diff = $date->diff($now);

        if ($diff->y > 0) {
            return $diff->y . ' tahun yang lalu';
        }
        if ($diff->m > 0) {
            return $diff->m . ' bulan yang lalu';
        }
        if ($diff->d > 0) {
            return $diff->d . ' hari yang lalu';
        }
        if ($diff->h > 0) {
            return $diff->h . ' jam yang lalu';
        }
        if ($diff->i > 0) {
            return $diff->i . ' menit yang lalu';
        }
        return 'baru saja';
    }

    /**
     * Get Indonesian day name.
     */
    public static function getDayName(string|Carbon $date): string
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        return $days[$date->dayName] ?? $date->dayName;
    }

    /**
     * Get Indonesian month name.
     */
    public static function getMonthName(int|string $month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        if (is_string($month)) {
            $month = Carbon::parse($month)->month;
        }

        return $months[$month] ?? '';
    }

    /**
     * Check if date is today.
     */
    public static function isToday(string|Carbon $date): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $date->isToday();
    }

    /**
     * Check if date is in current week.
     */
    public static function isThisWeek(string|Carbon $date): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $date->isCurrentWeek();
    }

    /**
     * Check if date is in current month.
     */
    public static function isThisMonth(string|Carbon $date): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $date->isCurrentMonth();
    }

    /**
     * Get school year string.
     */
    public static function schoolYear(int $start, int $end = null): string
    {
        if (!$end) {
            $end = $start + 1;
        }
        return "$start/" . substr($end, -2);
    }

    /**
     * Get current school year.
     */
    public static function currentSchoolYear(): string
    {
        $now = Carbon::now();

        // School year typically starts in July
        if ($now->month >= 7) {
            return self::schoolYear($now->year, $now->year + 1);
        }

        return self::schoolYear($now->year - 1, $now->year);
    }

    /**
     * Get academic semester.
     */
    public static function currentSemester(): int
    {
        $now = Carbon::now();

        // Semester 1: July - December
        // Semester 2: January - June
        return $now->month >= 7 ? 1 : 2;
    }
}
