<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Violation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentService
{
    /**
     * Create a new student.
     */
    public function create(array $data, int $classId): Student
    {
        // Set default poin if not provided
        $data['poin'] = $data['poin'] ?? 100;
        $data['class_id'] = $classId;

        return Student::create($data);
    }

    /**
     * Update a student.
     */
    public function update(Student $student, array $data): Student
    {
        $student->update($data);
        return $student->fresh();
    }

    /**
     * Deactivate a student (soft delete).
     */
    public function deactivate(Student $student): Student
    {
        $student->update(['is_active' => false]);
        return $student;
    }

    /**
     * Reactivate a student.
     */
    public function reactivate(Student $student): Student
    {
        $student->update(['is_active' => true]);
        return $student;
    }

    /**
     * Reset student poin to 100.
     */
    public function resetPoin(Student $student): Student
    {
        $student->update(['poin' => 100]);
        return $student;
    }

    /**
     * Add violation and reduce poin.
     */
    public function addViolation(Student $student, array $violationData): \App\Models\Violation
    {
        $poinMap = [
            'ringan' => 5,
            'sedang' => 10,
            'berat' => 15,
        ];

        $poinReduced = $poinMap[$violationData['severity']] ?? 5;

        $violation = \App\Models\Violation::create([
            'student_id' => $student->id,
            'user_id' => auth()->id(),
            'class_id' => $student->class_id,
            'category' => $violationData['category'],
            'description' => $violationData['description'],
            'severity' => $violationData['severity'],
            'date' => $violationData['date'] ?? now(),
            'poin_reduced' => $poinReduced,
            'poin_before' => $student->poin,
            'poin_after' => max(0, $student->poin - $poinReduced),
            'status' => 'approved',
        ]);

        // Update student poin
        $student->decrement('poin', $poinReduced);

        return $violation;
    }

    /**
     * Remove violation and restore poin.
     */
    public function removeViolation(\App\Models\Violation $violation): bool
    {
        $violation->student->increment('poin', $violation->poin_reduced);
        return $violation->delete();
    }

    /**
     * Import students from array.
     */
    public function importFromArray(array $data, int $classId): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($data as $index => $row) {
            try {
                // Validate required fields
                if (empty($row['name'])) {
                    throw new \Exception('Nama wajib diisi');
                }

                $this->create([
                    'name' => $row['name'],
                    'nisn' => $row['nisn'] ?? null,
                    'nis' => $row['nis'] ?? null,
                    'gender' => $this->normalizeGender($row['gender'] ?? null),
                    'birth_date' => $this->parseDate($row['birth_date'] ?? null),
                    'birth_place' => $row['birth_place'] ?? null,
                    'religion' => $row['religion'] ?? null,
                    'address' => $row['address'] ?? null,
                    'father_name' => $row['father_name'] ?? null,
                    'mother_name' => $row['mother_name'] ?? null,
                    'parent_phone' => $row['parent_phone'] ?? null,
                    'parent_whatsapp' => $row['parent_whatsapp'] ?? $row['parent_phone'] ?? null,
                ], $classId);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'row' => $index + 2, // +2 for header and 0-index
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Normalize gender value.
     */
    protected function normalizeGender(?string $gender): ?string
    {
        if (!$gender) {
            return null;
        }

        $gender = strtolower(trim($gender));

        if (in_array($gender, ['laki-laki', 'laki', 'male', 'm', 'l'])) {
            return 'laki-laki';
        }

        if (in_array($gender, ['perempuan', 'female', 'f', 'p'])) {
            return 'perempuan';
        }

        return null;
    }

    /**
     * Parse date from various formats.
     */
    protected function parseDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
