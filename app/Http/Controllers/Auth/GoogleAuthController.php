<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists, login directly
                Auth::login($user);
                return $this->redirectAfterLogin();
            }

            // Check if user exists with same email
            $userByEmail = User::where('email', $googleUser->email)->first();

            if ($userByEmail) {
                // Link Google account to existing user
                $userByEmail->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);

                Auth::login($userByEmail);
                return $this->redirectAfterLogin();
            }

            // New user - store Google data in session and redirect to organization selection
            session(['google_user' => [
                'google_id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar,
            ]]);

            return redirect()->route('auth.setup');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Show organization setup page for new users
     */
    public function setup()
    {
        $googleUser = session('google_user');

        if (!$googleUser) {
            return redirect()->route('login');
        }

        // Get existing organizations for search
        $organizations = Organization::active()
            ->orderBy('name')
            ->get();

        return view('auth.setup', [
            'googleUser' => $googleUser,
            'organizations' => $organizations,
        ]);
    }

    /**
     * Complete registration with organization
     */
    public function completeRegistration(Request $request)
    {
        $request->validate([
            'organization_choice' => 'required|in:new,existing',
            'organization_id' => 'required_if:organization_choice,existing|exists:organizations,id',
            'organization_name' => 'required_if:organization_choice,new|max:255',
            'organization_type' => 'required_if:organization_choice,new',
            'organization_city' => 'required_if:organization_choice,new|max:100',
        ]);

        $googleUser = session('google_user');

        if (!$googleUser) {
            return redirect()->route('login');
        }

        try {
            DB::beginTransaction();

            // Handle organization
            if ($request->organization_choice === 'new') {
                $organization = Organization::create([
                    'name' => $request->organization_name,
                    'slug' => Organization::generateSlug($request->organization_name),
                    'type' => $request->organization_type,
                    'city' => $request->organization_city,
                    'email' => $googleUser['email'],
                    'status' => 'active',
                ]);
            } else {
                $organization = Organization::findOrFail($request->organization_id);
            }

            // Create user (password is required but can be null for Google OAuth)
            $user = User::create([
                'name' => $googleUser['name'],
                'email' => $googleUser['email'],
                'google_id' => $googleUser['google_id'],
                'avatar' => $googleUser['avatar'],
                'organization_id' => $organization->id,
                'role' => User::ROLE_WALAS,
                'password' => bcrypt(Str::random(32)), // Random password for Google users
                'email_verified_at' => now(), // Auto verify for Google users
                'is_active' => true,
            ]);

            // Clear session
            session()->forget('google_user');

            // Login and redirect
            Auth::login($user);

            DB::commit();

            return $this->redirectAfterLogin();

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors([
                'organization_name' => 'Terjadi kesalahan. Silakan coba lagi.',
            ])->withInput();
        }
    }

    /**
     * Redirect based on user role
     */
    private function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin) {
            return redirect()->route('admin.dashboard');
        }

        // Check if user has organization
        if (!$user->organization_id) {
            return redirect()->route('auth.setup');
        }

        return redirect()->route('dashboard');
    }
}
