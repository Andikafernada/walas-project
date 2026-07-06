<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBook extends Model{
    protected $table = 'cash_books';

    protected $fillable = [
        'class_id',
        'user_id',
        'type',
        'category',
        'description',
        'amount',
        'date',
        'receipt',
        'student_id',
        'created_by_name',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    const CATEGORIES = [
        'iuran_bulanan' => 'Iuran Bulanan',
        'kegiatan' => 'Dana Kegiatan',
        'ujian' => 'Ujian/Seragam',
        'spp' => 'SPP',
        'lainnya' => 'Lainnya',
        'snack' => 'Snack/Makan',
        'atk' => 'Alat Tulis Kantor',
        'transport' => 'Transport',
        'penghargaan' => 'Penghargaan',
        'perbaikan' => 'Perbaikan/Rusak',
    ];

    public function classModel(): BelongsTo{
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo{
        return $this->belongsTo(Student::class);
    }

    public function getFormattedAmountAttribute(): string{
        $prefix = $this->type === self::TYPE_INCOME ? '+' : '-';
        return $prefix . 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

class CashBookQueue extends Model{
    protected $table = 'cash_books';

    public function student(): BelongsTo{
        return $this->belongsTo(Student::class);
    }

    public function classModel(): BelongsTo{
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string{
        $prefix = $this->type === 'income' ? '+' : '-';
        return $prefix . 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
