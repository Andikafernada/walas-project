<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_organizations' => Organization::count(),
            'active_organizations' => Organization::where('status', 'active')->count(),
            'total_walas' => User::where('role', User::ROLE_WALAS)->count(),
            'total_students' => \App\Models\Student::count(),
            'new_this_month' => Organization::whereMonth('created_at', now()->month)->count(),
        ];

        $recentOrganizations = Organization::withCount('users')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrganizations'));
    }
}
