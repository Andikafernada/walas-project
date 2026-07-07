<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationStructure extends Model
{
    protected $table = 'organization_structures';

    protected $fillable = [
        'class_id',
        'student_id',
        'position',
        'academic_year',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    const POSITIONS = [
        'ketua_kelas' => 'Ketua Kelas',
        'wakil_ketua' => 'Wakil Ketua',
        'sekretaris' => 'Sekretaris',
        'bendahara' => 'Bendahara',
        'seksi_kehadiran' => 'Seksi Kehadiran',
        'seksi_barang' => 'Seksi Barang Hilang/Rusak',
        'seksi_kebersihan' => 'Seksi Kebersihan',
        'seksi_keamanan' => 'Seksi Keamanan',
        'seksi_olahraga' => 'Seksi Olahraga',
        'seksi_kesenian' => 'Seksi Kesenian',
    ];

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
