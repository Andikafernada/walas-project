<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->apiTokens()->latest()->get();

        return view('dashboard.api-tokens.index', compact('tokens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'abilities' => 'required|array',
            'abilities.*' => 'in:read,write,exam_monitor,attendance,cbt',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $plainToken = Str::random(64);

        $token = auth()->user()->apiTokens()->create([
            'name' => $validated['name'],
            'token' => hash('sha256', $plainToken),
            'abilities' => $validated['abilities'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        // Return the plain token only once
        return redirect()->route('api-tokens.index')
            ->with('success', 'API Token berhasil dibuat!')
            ->with('plain_token', $plainToken);
    }

    public function destroy(ApiToken $token)
    {
        $this->authorize('delete', $token);

        $token->delete();

        return back()->with('success', 'API Token berhasil dihapus!');
    }
}
