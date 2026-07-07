<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(ClassModel $class)
    {
        $this->authorize('viewAny', Schedule::class);

        $schedules = $class->schedules()
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('dashboard.schedules.index', compact('class', 'schedules'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', Schedule::class);

        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'teacher_name' => 'nullable|string|max:100',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $class->schedules()->create($validated);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, ClassModel $class, Schedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'teacher_name' => 'nullable|string|max:100',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);

        return back()->with('success', 'Jadwal berhasil diupdate!');
    }

    public function destroy(ClassModel $class, Schedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function import(Request $request, ClassModel $class)
    {
        $this->authorize('import', Schedule::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
        ]);

        // Implementation for Excel/CSV import
        // ...

        return back()->with('success', 'Jadwal berhasil diimpor!');
    }
}
