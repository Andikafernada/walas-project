<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index()
    {
        $classes = auth()->user()->classes()->with('students')->latest()->get();
        return view('dashboard.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'jurusan' => 'required|string|max:100',
            'alias' => 'nullable|string|max:50',
            'tingkat' => 'nullable|string|max:10',
            'school_year_start' => 'nullable|integer|min:2020|max:2030',
            'school_year_end' => 'nullable|integer|min:2020|max:2030',
        ]);

        $class = auth()->user()->classes()->create($validated);

        return redirect()->route('classes.show', $class)->with('success', 'Kelas berhasil dibuat!');
    }

    public function show(ClassModel $class)
    {
        $this->authorize('view', $class);

        $class->load(['students' => fn($q) => $q->where('is_active', true)]);
        $schedules = $class->schedules()->get()->groupBy('day');

        return view('dashboard.classes.show', compact('class', 'schedules'));
    }

    public function update(Request $request, ClassModel $class)
    {
        $this->authorize('update', $class);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'jurusan' => 'required|string|max:100',
            'alias' => 'nullable|string|max:50',
            'tingkat' => 'nullable|string|max:10',
        ]);

        $class->update($validated);

        return back()->with('success', 'Kelas berhasil diupdate!');
    }

    public function destroy(ClassModel $class)
    {
        $this->authorize('delete', $class);
        $class->delete();
        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
