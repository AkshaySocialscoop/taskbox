<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Step 1: Redirect to Google login
     */
    public function redirect()
    {
    return Socialite::driver('google')
        ->scopes(['openid', 'profile', 'email'])
        ->with(['prompt' => 'select_account']) 
        ->redirect();
    }

    public function callback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Store Google info in session
        session([
            'google_id'    => $googleUser->id,
            'google_name'  => $googleUser->name,
            'google_email' => $googleUser->email,
            'google_avatar'=> $googleUser->avatar,
        ]);

        return redirect()->route('googlechat.index');

    } catch (\Exception $e) {
        dd('OAuth failed', $e->getMessage(), $e->getTraceAsString());
    }
}

public function logout()
{
    // Clear only Google session data
    session()->forget(['google_id', 'google_name', 'google_email', 'google_avatar']);

    // Redirect back to googlechat (same page, now shows login button)
    return redirect()->route('googlechat.index');
}

 
     


   


}
