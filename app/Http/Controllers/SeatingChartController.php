<?php

namespace App\Http\Controllers;

use App\Models\SeatingChart;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class SeatingChartController extends Controller
{
    public function index(ClassModel $class)
    {
        $this->authorize('viewAny', SeatingChart::class);

        $charts = $class->seatingCharts()
            ->latest('effective_date')
            ->get();

        return view('dashboard.seating-charts.index', compact('class', 'charts'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', SeatingChart::class);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'layout' => 'required|array',
            'layout.rows' => 'required|integer|min:1|max:20',
            'layout.cols' => 'required|integer|min:1|max:20',
            'effective_date' => 'required|date',
            'expired_date' => 'nullable|date|after:effective_date',
            'assignments' => 'nullable|array',
        ]);

        // Deactivate previous charts
        $class->seatingCharts()->update(['is_active' => false]);

        $class->seatingCharts()->create([
            'name' => $validated['name'],
            'layout' => $validated['layout'],
            'effective_date' => $validated['effective_date'],
            'expired_date' => $validated['expired_date'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Denah tempat duduk berhasil dibuat!');
    }

    public function update(Request $request, ClassModel $class, SeatingChart $chart)
    {
        $this->authorize('update', $chart);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'layout' => 'nullable|array',
            'layout.rows' => 'nullable|integer|min:1|max:20',
            'layout.cols' => 'nullable|integer|min:1|max:20',
            'effective_date' => 'required|date',
            'expired_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $chart->update($validated);

        return back()->with('success', 'Denah berhasil diupdate!');
    }

    public function destroy(ClassModel $class, SeatingChart $chart)
    {
        $this->authorize('delete', $chart);

        $chart->delete();

        return back()->with('success', 'Denah berhasil dihapus!');
    }

    public function print(ClassModel $class, SeatingChart $chart)
    {
        $this->authorize('print', $chart);

        return view('dashboard.seating-charts.print', compact('class', 'chart'));
    }
}
