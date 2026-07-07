<?php

namespace App\Http\Controllers;

use App\Models\CashBook;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class CashBookController extends Controller
{
    public function index(ClassModel $class, Request $request)
    {
        $this->authorize('viewAny', CashBook::class);

        $query = $class->cashBooks();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $transactions = $query->latest('date')->paginate(20);

        // Calculate totals
        $totals = [
            'income' => $class->cashBooks()->where('type', 'income')->sum('amount'),
            'expense' => $class->cashBooks()->where('type', 'expense')->sum('amount'),
            'balance' => $class->cashBooks()->where('type', 'income')->sum('amount')
                - $class->cashBooks()->where('type', 'expense')->sum('amount'),
        ];

        return view('dashboard.cashbook.index', compact('class', 'transactions', 'totals'));
    }

    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('create', CashBook::class);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'student_id' => 'nullable|exists:students,id',
            'receipt' => 'nullable|file|max:2048',
        ]);

        $class->cashBooks()->create([
            ...$validated,
            'user_id' => auth()->id(),
            'created_by_name' => auth()->user()->name,
        ]);

        return back()->with('success', 'Transaksi berhasil dicatat!');
    }

    public function update(Request $request, ClassModel $class, CashBook $cashBook)
    {
        $this->authorize('update', $cashBook);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $cashBook->update($validated);

        return back()->with('success', 'Transaksi berhasil diupdate!');
    }

    public function destroy(ClassModel $class, CashBook $cashBook)
    {
        $this->authorize('delete', $cashBook);

        $cashBook->delete();

        return back()->with('success', 'Transaksi berhasil dihapus!');
    }

    public function export(ClassModel $class, Request $request)
    {
        $this->authorize('export', CashBook::class);

        // Export to Excel implementation
        // ...

        return back()->with('info', 'Fitur export dalam pengembangan.');
    }
}
