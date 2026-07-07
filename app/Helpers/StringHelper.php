<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * Generate initials from name.
     */
    public static function initials(string $name, int $limit = 2): string
    {
        $words = preg_split('/[\s-]+/', $name);
        $initials = array_slice($words, 0, $limit);
        $initials = array_map(fn($word) => strtoupper($word[0] ?? ''), $initials);

        return implode('', $initials);
    }

    /**
     * Format phone number for WhatsApp.
     */
    public static function formatWhatsAppNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle various formats
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }

    /**
     * Mask phone number for display.
     */
    public static function maskPhone(?string $phone): string
    {
        if (!$phone) {
            return '-';
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 4) {
            return $phone;
        }

        $visible = substr($phone, -4);
        $masked = str_repeat('*', strlen($phone) - 4);

        return $masked . $visible;
    }

    /**
     * Truncate text with ellipsis.
     */
    public static function truncate(string $text, int $length = 50, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }

    /**
     * Generate slug from string.
     */
    public static function slug(string $text): string
    {
        $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }

    /**
     * Format currency (Indonesian Rupiah).
     */
    public static function formatRupiah(float|int $amount, bool $withSymbol = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');

        if ($withSymbol) {
            return 'Rp ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Parse currency string to number.
     */
    public static function parseRupiah(string $amount): int
    {
        $amount = preg_replace('/[^0-9]/', '', $amount);
        return (int) $amount;
    }

    /**
     * Format NISN (10 digits).
     */
    public static function formatNisn(?string $nisn): string
    {
        if (!$nisn) {
            return '-';
        }

        return str_pad($nisn, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Clean name (remove extra spaces, capitalize).
     */
    public static function cleanName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);
        $name = ucwords(strtolower($name));

        return $name;
    }

    /**
     * Check if string is valid Indonesian phone number.
     */
    public static function isValidPhone(?string $phone): bool
    {
        if (!$phone) {
            return false;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Indonesian phone numbers are typically 10-13 digits
        if (strlen($phone) < 10 || strlen($phone) > 13) {
            return false;
        }

        // Should start with 0, 62, or +
        return preg_match('/^(\+|0|62)/', $phone) === 1;
    }

    /**
     * Generate random string.
     */
    public static function random(int $length = 16, string $charset = 'alphanumeric'): string
    {
        $charsets = [
            'alphanumeric' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numeric' => '0123456789',
            'hex' => '0123456789abcdef',
        ];

        $chars = $charsets[$charset] ?? $charsets['alphanumeric'];
        $result = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    /**
     * Convert to title case (Indonesian aware).
     */
    public static function titleCase(string $text): string
    {
        // List of words that should not be capitalized
        $lowercase = ['di', 'ke', 'dari', 'dan', 'atau', 'yang', 'untuk', 'dengan', 'pada', 'ini', 'itu'];

        $words = explode(' ', strtolower($text));
        $result = [];

        foreach ($words as $key => $word) {
            if ($key === 0 || !in_array($word, $lowercase)) {
                $result[] = ucfirst($word);
            } else {
                $result[] = $word;
            }
        }

        return implode(' ', $result);
    }
}
