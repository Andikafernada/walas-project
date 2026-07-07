<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(ClassModel $class, Request $request)
    {
        $this->authorize('viewAny', Violation::class);

        $query = $class->violations()->with('student');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $violations = $query->latest('date')->paginate(20);
        $students = $class->students()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.violations.index', compact('class', 'violations', 'students'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', Violation::class);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|in:' . implode(',', array_keys(Violation::CATEGORIES)),
            'description' => 'required|string|max:500',
            'severity' => 'required|in:ringan,sedang,berat',
            'date' => 'required|date',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Calculate poin
        $poinMap = [
            'ringan' => 5,
            'sedang' => 10,
            'berat' => 15,
        ];

        $violation = $class->violations()->create([
            'student_id' => $student->id,
            'user_id' => auth()->id(),
            'category' => $validated['category'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'date' => $validated['date'],
            'poin_reduced' => $poinMap[$validated['severity']],
            'poin_before' => $student->poin,
            'poin_after' => max(0, $student->poin - $poinMap[$validated['severity']]),
            'status' => 'approved',
        ]);

        // Update student poin
        $student->decrement('poin', $poinMap[$validated['severity']]);

        return back()->with('success', 'Pelanggaran berhasil dicatat!');
    }

    public function update(Request $request, ClassModel $class, Violation $violation)
    {
        $this->authorize('update', $violation);

        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(Violation::CATEGORIES)),
            'description' => 'required|string|max:500',
            'severity' => 'required|in:ringan,sedang,berat',
            'date' => 'required|date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $violation->update($validated);

        return back()->with('success', 'Pelanggaran berhasil diupdate!');
    }

    public function destroy(ClassModel $class, Violation $violation)
    {
        $this->authorize('delete', $violation);

        // Restore poin
        $violation->student->increment('poin', $violation->poin_reduced);

        $violation->delete();

        return back()->with('success', 'Pelanggaran berhasil dihapus!');
    }
}
