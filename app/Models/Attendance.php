<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'user_id',
        'date',
        'status',
        'notes',
        'minutes_late',
        'attachment',
    ];

    protected $casts = [
        'date' => 'date',
        'minutes_late' => 'integer',
    ];

    // Status constants
    const STATUS_HADIR = 'hadir';
    const STATUS_TERLAMBAT = 'terlambat';
    const STATUS_SAKIT = 'sakit';
    const STATUS_IZIN = 'izin';
    const STATUS_ALPA = 'alpa';

    // Relations
    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                self::STATUS_HADIR => 'Hadir',
                self::STATUS_TERLAMBAT => 'Terlambat',
                self::STATUS_SAKIT => 'Sakit',
                self::STATUS_IZIN => 'Izin',
                self::STATUS_ALPA => 'Alpa',
                default => $this->status,
            }
        );
    }
}
