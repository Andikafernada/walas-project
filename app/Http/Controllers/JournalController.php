<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(ClassModel $class, Request $request)
    {
        $this->authorize('viewAny', Journal::class);

        $query = $class->journals()->with('student');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        $journals = $query->latest('date')->paginate(20);
        $students = $class->students()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.journals.index', compact('class', 'journals', 'students'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', Journal::class);

        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(Journal::CATEGORIES)),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'date' => 'required|date',
            'student_id' => 'nullable|exists:students,id',
            'outcome' => 'nullable|string|max:255',
            'follow_up' => 'nullable|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $class->journals()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Catatan journal berhasil ditambahkan!');
    }

    public function update(Request $request, ClassModel $class, Journal $journal)
    {
        $this->authorize('update', $journal);

        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(Journal::CATEGORIES)),
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'date' => 'required|date',
            'student_id' => 'nullable|exists:students,id',
            'outcome' => 'nullable|string|max:255',
            'follow_up' => 'nullable|string',
        ]);

        $journal->update($validated);

        return back()->with('success', 'Journal berhasil diupdate!');
    }

    public function destroy(ClassModel $class, Journal $journal)
    {
        $this->authorize('delete', $journal);

        $journal->delete();

        return back()->with('success', 'Journal berhasil dihapus!');
    }
}
