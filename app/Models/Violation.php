<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    protected $table = 'violations';

    protected $fillable = [
        'student_id',
        'user_id',
        'class_id',
        'category',
        'description',
        'poin_reduced',
        'poin_before',
        'poin_after',
        'severity',
        'date',
        'attachment',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'poin_reduced' => 'integer',
        'poin_before' => 'integer',
        'poin_after' => 'integer',
    ];

    const CATEGORIES = [
        'terlambat' => 'Terlambat',
        'tidak_mengerjakan_tugas' => 'Tidak Mengerjakan Tugas',
        'mengganggu_teman' => 'Mengganggu Teman',
        'merokok' => 'Merokok',
        'bolos' => 'Bolos',
        'tidak_uniform' => 'Tidak Uniform',
        'hp_di_kelas' => 'HP di Kelas',
        'tidak_sopan' => 'Tidak Sopan',
        'lainnya' => 'Lainnya',
    ];

    const SEVERITIES = [
        'ringan' => 'Ringan (-5 poin)',
        'sedang' => 'Sedang (-10 poin)',
        'berat' => 'Berat (-15 poin atau lebih)',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
