<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function upgrade()
    {
        return view('dashboard.subscription.upgrade');
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'tier' => 'required|in:pro,enterprise',
            'duration' => 'required|in:monthly,yearly',
            'payment_method' => 'nullable|string',
        ]);

        // Subscription integration (Midtrans, Xendit, etc.)
        // ...

        return redirect()->route('dashboard')
            ->with('success', 'Langganan berhasil diaktifkan!');
    }

    public function billing()
    {
        return view('dashboard.subscription.billing');
    }
}
