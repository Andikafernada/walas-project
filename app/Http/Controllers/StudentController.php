<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Attendance;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request, ClassModel $class)
    {
        $this->authorize('view', $class);

        $students = $class->students()
            ->where('is_active', true)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(20);

        return view('dashboard.students.index', compact('class', 'students'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'nisn' => 'nullable|string|max:20',
            'nis' => 'nullable|string|max:20',
            'name' => 'required|string|max:100',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_whatsapp' => 'nullable|string|max:20',
            'poin' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['class_id'] = $class->id;
        $validated['poin'] = $validated['poin'] ?? 100;

        $student = $class->students()->create($validated);

        return back()->with('success', "Siswa {$student->name} berhasil ditambahkan!");
    }

    public function update(Request $request, ClassModel $class, Student $student)
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'nisn' => 'nullable|string|max:20',
            'nis' => 'nullable|string|max:20',
            'name' => 'required|string|max:100',
            'gender' => 'nullable|in:laki-laki,perempuan',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'father_name' => 'nullable|string|max:100',
            'mother_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_whatsapp' => 'nullable|string|max:20',
        ]);

        $student->update($validated);

        return back()->with('success', 'Data siswa berhasil diupdate!');
    }

    public function destroy(ClassModel $class, Student $student)
    {
        $this->authorize('update', $class);

        $student->update(['is_active' => false]);

        return back()->with('success', 'Siswa berhasil dinonaktifkan.');
    }
}
