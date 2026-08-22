<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/chat.spaces.readonly',
                'https://www.googleapis.com/auth/chat.messages.readonly',
                'https://www.googleapis.com/auth/chat.memberships.readonly'
            ])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Get currently logged in user (from Breeze)
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Please login to your account first.');
            }

            // Store ALL Google data in database
            $user->update([
                'google_id' => $googleUser->id,
                'google_name' => $googleUser->name,
                'google_email' => $googleUser->email,
                'google_avatar' => $googleUser->avatar,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
            ]);

            return redirect()->route('googlechat.index')
                ->with('success', 'Google Chat connected successfully!');

        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return redirect()->route('googlechat.index')
                ->with('error', 'Failed to connect Google Chat. Please try again.');
        }
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            // Clear ALL Google data from database
            $user->update([
                'google_id' => null,
                'google_name' => null,
                'google_email' => null,
                'google_avatar' => null,
                'google_token' => null,
                'google_refresh_token' => null,
                'google_token_expires_at' => null,
            ]);
        }

        return redirect()->route('googlechat.index')
            ->with('success', 'Google Chat disconnected successfully!');
    }
}