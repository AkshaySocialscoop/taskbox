<?php

namespace App\Http\Controllers;

use App\Services\GoogleChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleChatController extends Controller
{
    public function index(Request $request, GoogleChatService $chatService)
    {
        try {
            // Check if user is logged in
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Please login first.');
            }

            $user = Auth::user();

            // Check Google connection
            $hasGoogle = !empty($user->google_id);
            $tokenExpired = $user->google_token_expires_at && now()->gte($user->google_token_expires_at);

            // Google user data
            $googleUser = null;
            if ($hasGoogle) {
                $googleUser = [
                    'name' => $user->google_name,
                    'email' => $user->google_email,
                    'avatar' => $user->google_avatar,
                ];
            }

            $spaces = [];
            $selectedSpace = null;
            $messages = [];

            if ($hasGoogle && !$tokenExpired) {
                // Get spaces
                $result = $chatService->listSpaces($user);
                $spaces = $result['spaces'] ?? [];

                // If space selected
                if ($request->has('space')) {
                    $spaceName = $request->space;

                    // Find selected space
                    foreach ($spaces as $space) {
                        if ($space['name'] === $spaceName) {
                            $selectedSpace = $space;
                            break;
                        }
                    }

                    // Get messages
                    if ($selectedSpace) {
                        $messagesResult = $chatService->getMessages($user, $spaceName);
                        $messages = $messagesResult['messages'] ?? [];
                    }
                }
            }

            return view('googlechat.index', [
                'user' => $user,
                'googleUser' => $googleUser,
                'spaces' => $spaces,
                'selectedSpace' => $selectedSpace,
                'messages' => $messages,
                'has_google' => $hasGoogle,
                'token_expired' => $tokenExpired,
                'total_spaces' => count($spaces),
            ]);

        } catch (\Exception $e) {
            Log::error('GoogleChatController error: '.$e->getMessage());

            return view('googlechat.index', [
                'user' => Auth::user(),
                'googleUser' => null,
                'spaces' => [],
                'selectedSpace' => null,
                'messages' => [],
                'has_google' => false,
                'token_expired' => false,
                'total_spaces' => 0,
                'error' => 'Unable to load page. Please try again later.',
            ]);
        }
    }
}