<?php

namespace App\Http\Controllers;

use App\Models\WaQueue;
use Illuminate\Http\Request;

class WaQueueController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->waQueues();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $queues = $query->latest()->paginate(30);

        $stats = [
            'pending' => auth()->user()->waQueues()->where('status', 'pending')->count(),
            'processing' => auth()->user()->waQueues()->where('status', 'processing')->count(),
            'sent' => auth()->user()->waQueues()->where('status', 'sent')->count(),
            'failed' => auth()->user()->waQueues()->where('status', 'failed')->count(),
        ];

        return view('dashboard.wa-queue.index', compact('queues', 'stats'));
    }

    public function retry(WaQueue $queue)
    {
        $this->authorize('retry', $queue);

        $queue->update([
            'status' => 'pending',
            'attempts' => 0,
            'error' => null,
        ]);

        return back()->with('success', 'Pesan akan dikirim ulang.');
    }

    public function destroy(WaQueue $queue)
    {
        $this->authorize('delete', $queue);

        $queue->delete();

        return back()->with('success', 'Queue berhasil dihapus!');
    }

    public function bulkSend(Request $request)
    {
        $this->authorize('sendBulk', WaQueue::class);

        $validated = $request->validate([
            'type' => 'required|in:attendance,warning,announcement,report',
            'message' => 'required|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Implementation for bulk send
        // ...

        return back()->with('success', 'Pesan massal sedang diproses.');
    }
}
