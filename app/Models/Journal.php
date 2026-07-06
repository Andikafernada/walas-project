<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $table = 'journals';

    protected $fillable = [
        'user_id',
        'student_id',
        'class_id',
        'category',
        'subject',
        'content',
        'date',
        'outcome',
        'follow_up',
        'attachment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    const CATEGORIES = [
        'konseling' => 'Konseling Individual',
        'kelompok' => 'Konseling Kelompok',
        'home_visit' => ' Kunjungan Rumah',
        'call_parent' => 'Telepon Orang Tua',
        'gurubk' => 'Rujukan Guru BK',
        'admin' => 'Administrasi BK',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
