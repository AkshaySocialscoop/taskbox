<?php

namespace App\Http\Controllers;

use App\Services\GoogleChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleChatController extends Controller
{
    /**
     * Show spaces and optionally messages if a space is selected.
     */
    public function index(Request $request, GoogleChatService $chatService)
    {
        try {
            // Ensure user is authenticated with Google
            if (!session()->has('google_token')) {
                return view('googlechat.index', [
                    'spaces'   => [],
                    'messages' => []
                ]);
            }

            $token   = session('google_token');
            $spaces  = $chatService->listSpaces($token);
            $messages = [];

            // If a space is selected, fetch its messages
            if ($request->has('space')) {
                $messages = $chatService->listMessages($token, $request->space);

                // Resolve sender names safely
                foreach ($messages as &$msg) {
                    if (!empty($msg['sender']['name'])) {
                        try {
                            $userInfo = $chatService->getUserInfo($token, $msg['sender']['name']);
                            $msg['sender']['displayName'] = $userInfo['displayName'] ?? $msg['sender']['name'];
                            $msg['sender']['avatar']      = $userInfo['avatar'] ?? null;
                            $msg['sender']['email']       = $userInfo['email'] ?? null;
                        } catch (\Exception $e) {
                            Log::error("Failed to fetch user info: " . $e->getMessage());
                            $msg['sender']['displayName'] = $msg['sender']['name'];
                        }
                    }
                }
            }

            return view('googlechat.index', [
                'id'       => session('google_id'),
                'name'     => session('google_name'),
                'email'    => session('google_email'),
                'avatar'   => session('google_avatar'),
                'spaces'   => $spaces,
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            Log::error("GoogleChatController@index error: " . $e->getMessage());
            return view('googlechat.index', [
                'spaces'   => [],
                'messages' => [],
                'error'    => 'Unable to load Google Chat data. Please try again later.'
            ]);
        }
    }

    /**
     * Dedicated messages view (optional if you want separate page).
     */
    public function messages(GoogleChatService $chatService, $space)
    {
        try {
            if (!session()->has('google_token')) {
                return redirect()->route('googlechat.index');
            }

            $token    = session('google_token');
            $messages = $chatService->listMessages($token, $space);

            return view('googlechat.messages', [
                'space'    => $space,
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            Log::error("GoogleChatController@messages error: " . $e->getMessage());
            return redirect()->route('googlechat.index')
                ->with('error', 'Unable to load messages for this space.');
        }
    }
}
