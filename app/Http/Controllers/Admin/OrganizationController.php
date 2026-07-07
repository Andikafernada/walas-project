<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Display a listing of organizations.
     */
    public function index(Request $request)
    {
        $query = Organization::withCount(['users', 'classes']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $organizations = $query->orderBy('created_at', 'desc')->paginate(15);

        // Stats
        $stats = [
            'total' => Organization::count(),
            'active' => Organization::where('status', 'active')->count(),
            'pending' => Organization::where('status', 'pending')->count(),
            'total_users' => User::where('role', 'walas')->count(),
        ];

        return view('admin.organizations.index', compact('organizations', 'stats'));
    }

    /**
     * Show organization details.
     */
    public function show(Organization $organization)
    {
        $organization->load(['users', 'classes.students']);

        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * Update organization status.
     */
    public function updateStatus(Request $request, Organization $organization)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending,suspended',
        ]);

        $organization->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status organisasi berhasil diperbarui.');
    }

    /**
     * Get users for an organization (API).
     */
    public function users(Organization $organization)
    {
        $users = $organization->users()
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }
}
