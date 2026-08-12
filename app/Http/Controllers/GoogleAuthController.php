<?php

namespace App\Http\Controllers;

use App\Models\GoogleAccount;
use Google\Client;
use Google\Service\Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect TaskBox user to Google OAuth.
     */
    public function redirect(Request $request)
    {
        $client = new Client();

        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));

        // Basic Google account information
        $client->setScopes([
            'openid',
            'email',
            'profile',

            // Chat
            'https://www.googleapis.com/auth/chat.spaces.readonly',
            'https://www.googleapis.com/auth/chat.messages',
            'https://www.googleapis.com/auth/chat.messages.create',
            'https://www.googleapis.com/auth/chat.messages.readonly',
            'https://www.googleapis.com/auth/chat.memberships.readonly',

            // Calendar
            'https://www.googleapis.com/auth/calendar',

            // Gmail
            'https://www.googleapis.com/auth/gmail.modify',

            // Drive
            'https://www.googleapis.com/auth/drive',

            // People
            'https://www.googleapis.com/auth/contacts.readonly',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
        ]);

        // We need refresh_token for future API access.
        $client->setAccessType('offline');

        // Ask Google for consent.
        $client->setPrompt('consent');

        // Allow incremental authorization later.
        $client->setIncludeGrantedScopes(true);

        // Generate OAuth state for security.
        $state = bin2hex(random_bytes(32));

        session([
            'google_oauth_state' => $state,
        ]);

        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Google redirects back here after authorization.
     */
    public function callback(Request $request)
    {
        // Check OAuth state
        $sessionState = session('google_oauth_state');

        if (
            !$sessionState ||
            !$request->state ||
            !hash_equals($sessionState, $request->state)
        ) {
            return redirect('/integration')
                ->with('error', 'Invalid Google authorization request.');
        }

        // Remove state after verification
        session()->forget('google_oauth_state');

        // Google returned an error
        if ($request->filled('error')) {
            return redirect('/integration')
                ->with(
                    'error',
                    'Google authorization was cancelled or denied.'
                );
        }

        // Authorization code is required
        if (!$request->filled('code')) {
            return redirect('/integration')
                ->with('error', 'Google authorization code was not received.');
        }

        try {

            $client = new Client();

            $client->setClientId(config('google.client_id'));
            $client->setClientSecret(config('google.client_secret'));
            $client->setRedirectUri(config('google.redirect_uri'));

            $client->setScopes([
                'openid',
                'email',
                'profile',

                // Chat
                'https://www.googleapis.com/auth/chat.spaces.readonly',
                'https://www.googleapis.com/auth/chat.messages',
                'https://www.googleapis.com/auth/chat.messages.create',
                'https://www.googleapis.com/auth/chat.messages.readonly',
                'https://www.googleapis.com/auth/chat.memberships.readonly',

                // Calendar
                'https://www.googleapis.com/auth/calendar',

                // Gmail
                'https://www.googleapis.com/auth/gmail.modify',

                // Drive
                'https://www.googleapis.com/auth/drive',

                // People
                'https://www.googleapis.com/auth/contacts.readonly',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email',
            ]);

            // Exchange authorization code for tokens
            $token = $client->fetchAccessTokenWithAuthCode(
                $request->code
            );

            // Check Google response
            if (isset($token['error'])) {

                Log::error('Google OAuth token error', [
                    'error' => $token['error'],
                    'description' => $token['error_description'] ?? null,
                ]);

                return redirect('/integration')
                    ->with('error', 'Unable to connect Google account.');
            }

            // Get Google account information
            $client->setAccessToken($token);

            $oauth2 = new Oauth2($client);

            $googleUser = $oauth2->userinfo->get();

            $accessToken = $token['access_token'] ?? null;
            $refreshToken = $token['refresh_token'] ?? null;

            // Calculate expiry
            $expiresAt = null;

            if (isset($token['expires_in'])) {
                $expiresAt = now()->addSeconds(
                    $token['expires_in']
                );
            }

            /*
             * Save / update Google account for
             * currently logged-in TaskBox user.
             */
            GoogleAccount::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                ],
                [
                    'google_user_id' => $googleUser->id,
                    'google_email' => $googleUser->email,
                    'access_token' => $accessToken,

                    // If Google doesn't return a new refresh token,
                    // keep the existing one.
                    'refresh_token' => $refreshToken
                        ?: GoogleAccount::where(
                            'user_id',
                            auth()->id()
                        )->value('refresh_token'),

                    'token_expires_at' => $expiresAt,

                    'scopes' => json_encode(
                        $token['scope'] ?? []
                    ),
                ]
            );

            return redirect('/integration')
                ->with(
                    'success',
                    'Google account connected successfully!'
                );
        } catch (\Throwable $e) {

            Log::error('Google OAuth callback failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect('/integration')
                ->with(
                    'error',
                    'Something went wrong while connecting Google.'
                );
        }
    }

    public function testConnection()
    {
        $user = auth()->user();

        $account = \App\Models\GoogleAccount::where('user_id', $user->id)
            ->firstOrFail();

        $client = new Client();

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($account->access_token);



        if (
            $account->token_expires_at &&
            now()->greaterThanOrEqualTo($account->token_expires_at)
        ) {

            if (!$account->refresh_token) {
                return back()->with(
                    'error',
                    'Google token expired and no refresh token is available. Please reconnect Google.'
                );
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $account->refresh_token
            );

            if (isset($newToken['error'])) {

                \Log::error('Google Token Refresh Failed', [
                    'user_id' => $user->id,
                    'error' => $newToken['error'] ?? null,
                    'error_description' => $newToken['error_description'] ?? null,
                ]);

                return back()->with(
                    'error',
                    'Google token refresh failed: ' .
                        ($newToken['error_description']
                            ?? $newToken['error']
                            ?? 'Unknown error')
                );
            }



            $account->update([
                'access_token' => $newToken['access_token'],
                'token_expires_at' => now()->addSeconds(
                    $newToken['expires_in'] ?? 3600
                ),
            ]);

            // Use the new token for this request
            $client->setAccessToken($newToken['access_token']);
        }



        try {

            $oauth2 = new Oauth2($client);

            $googleUser = $oauth2->userinfo->get();

            return back()->with(
                'success',
                'Google connected successfully: ' . $googleUser->email
            );
        } catch (\Throwable $e) {

            \Log::error('Google Connection Test Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Google connection failed: ' . $e->getMessage()
            );
        }
    }

    public function testChat()
    {
        $user = auth()->user();

        $account = GoogleAccount::where('user_id', $user->id)->firstOrFail();

        $client = new Client();

        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setAccessToken($account->access_token);

        // Refresh expired token
        if (
            $account->token_expires_at &&
            now()->greaterThanOrEqualTo($account->token_expires_at)
        ) {
            if (!$account->refresh_token) {
                return back()->with(
                    'error',
                    'Google token expired. Please reconnect Google.'
                );
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $account->refresh_token
            );

            if (isset($newToken['error'])) {
                return back()->with(
                    'error',
                    'Google token refresh failed.'
                );
            }

            $account->update([
                'access_token' => $newToken['access_token'],
                'token_expires_at' => now()->addSeconds(
                    $newToken['expires_in'] ?? 3600
                ),
            ]);

            $client->setAccessToken($newToken['access_token']);
        }

        try {

            $service = new \Google\Service\HangoutsChat($client);

            $spaces = $service->spaces->listSpaces();

            return response()->json([
                'success' => true,
                'spaces' => $spaces->toSimpleObject(),
            ]);
        } catch (\Throwable $e) {

            \Log::error('Google Chat API Test Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function disconnect()
    {
        $user = auth()->user();

        $account = GoogleAccount::where('user_id', $user->id)->first();

        if (!$account) {
            return back()->with(
                'error',
                'No Google account is connected.'
            );
        }

        try {
            // Revoke Google access if we have an access token
            if ($account->access_token) {
                $client = new Client();

                $client->setClientId(config('google.client_id'));
                $client->setClientSecret(config('google.client_secret'));

                $client->revokeToken($account->access_token);
            }
        } catch (\Throwable $e) {

            // Log the revoke failure, but still remove
            // the local connection from TaskBox.
            Log::warning('Google token revoke failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        // Remove Google connection from TaskBox
        $account->delete();

        return back()->with(
            'success',
            'Google account disconnected successfully.'
        );
    }
}
