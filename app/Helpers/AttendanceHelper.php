<?php

namespace App\Helpers;

class AttendanceHelper
{
    /**
     * Get status color for badge.
     */
    public static function statusColor(string $status): string
    {
        return match($status) {
            'hadir' => 'green',
            'terlambat' => 'yellow',
            'sakit' => 'blue',
            'izin' => 'purple',
            'alpa' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public static function statusLabel(string $status): string
    {
        return match($status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpa' => 'Alfa',
            'active' => 'Aktif',
            'used' => 'Selesai',
            'expired' => 'Kedaluwarsa',
            'pending' => 'Menunggu',
            default => ucfirst($status),
        };
    }

    /**
     * Get short status label.
     */
    public static function shortStatus(string $status): string
    {
        return match($status) {
            'hadir' => 'H',
            'terlambat' => 'T',
            'sakit' => 'S',
            'izin' => 'I',
            'alpa' => 'A',
            default => '-',
        };
    }

    /**
     * Calculate attendance rate.
     */
    public static function attendanceRate(int $present, int $total): float
    {
        if ($total === 0) {
            return 0;
        }

        return round(($present / $total) * 100, 2);
    }

    /**
     * Get attendance emoji.
     */
    public static function statusEmoji(string $status): string
    {
        return match($status) {
            'hadir' => '✅',
            'terlambat' => '⏰',
            'sakit' => '🏥',
            'izin' => '📝',
            'alpa' => '❌',
            default => '❓',
        };
    }

    /**
     * Get session status color.
     */
    public static function sessionStatusColor(string $status): string
    {
        return match($status) {
            'active' => 'yellow',
            'used' => 'green',
            'expired' => 'gray',
            'pending' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Format minutes late.
     */
    public static function formatLate(int $minutes): string
    {
        if ($minutes <= 0) {
            return '-';
        }

        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours . ' jam';
        }

        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }
}
