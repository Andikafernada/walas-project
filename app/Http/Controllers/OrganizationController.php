<?php

namespace App\Http\Controllers;

use App\Models\OrganizationStructure;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(ClassModel $class, Request $request)
    {
        $this->authorize('viewAny', OrganizationStructure::class);

        $academicYear = $request->get('academic_year', now()->year . '-' . (now()->year + 1));

        $structures = $class->organizationStructures()
            ->where('academic_year', $academicYear)
            ->with('student')
            ->get()
            ->groupBy('position');

        $students = $class->students()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.organization.index', compact('class', 'structures', 'students', 'academicYear'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', OrganizationStructure::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'position' => 'required|in:' . implode(',', array_keys(OrganizationStructure::POSITIONS)),
            'academic_year' => 'required|string',
        ]);

        // Check if position already occupied
        $existing = $class->organizationStructures()
            ->where('position', $validated['position'])
            ->where('academic_year', $validated['academic_year'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Posisi tersebut sudah terisi!');
        }

        $class->organizationStructures()->create([
            ...$validated,
            'is_active' => true,
        ]);

        return back()->with('success', 'Struktur organisasi berhasil ditambahkan!');
    }

    public function update(Request $request, ClassModel $class, OrganizationStructure $structure)
    {
        $this->authorize('update', $structure);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $structure->update($validated);

        return back()->with('success', 'Struktur berhasil diupdate!');
    }

    public function destroy(ClassModel $class, OrganizationStructure $structure)
    {
        $this->authorize('delete', $structure);

        $structure->delete();

        return back()->with('success', 'Struktur berhasil dihapus!');
    }
}
