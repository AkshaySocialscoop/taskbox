<?php

namespace App\Http\Controllers;

use App\Models\GoogleAccount;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleChatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Google Chat Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $account = $this->getGoogleAccount();

        if (!$account) {
            return redirect()
                ->route('integration')
                ->with('error', 'Please connect your Google account first.');
        }

        try {
            

            $account = $this->refreshTokenIfNeeded($account);

            $spaces = $this->getSpaces($account);

            /*
            |--------------------------------------------------------------------------
            | Developer Debug
            |--------------------------------------------------------------------------
            |
            | Open:
            | /google/chat?debug=1
            |
            */

            if (request()->boolean('debug')) {
                dd([
                    'google_account' => [
                        'id' => $account->id,
                        'user_id' => $account->user_id,
                        'google_user_id' => $account->google_user_id,
                        'google_email' => $account->google_email,
                        'token_expires_at' => $account->token_expires_at,
                        'scopes' => $account->scopes,
                    ],

                    'spaces_count' => count($spaces),

                    'spaces' => $spaces,
                ]);
            }

            return view('google.chat.index', [
                'googleAccount' => $account,
                'spaces' => $spaces,
            ]);

        } catch (\Throwable $e) {

            Log::error('Google Chat index failed', [
                'user_id' => auth()->id(),
                'account_id' => $account->id ?? null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with(
                'error',
                'Unable to load Google Chat. Please try again.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Messages
    |--------------------------------------------------------------------------
    */

    public function messages(Request $request, string $space)
    {
        $account = $this->getGoogleAccount();

        if (!$account) {
            return response()->json([
                'success' => false,
                'error' => 'Google account is not connected.',
            ], 401);
        }

        try {

            $account = $this->refreshTokenIfNeeded($account);

            /*
            |--------------------------------------------------------------------------
            | Normalize space name
            |--------------------------------------------------------------------------
            */

            if (!str_starts_with($space, 'spaces/')) {
                $space = 'spaces/' . $space;
            }

            $url = "https://chat.googleapis.com/v1/{$space}/messages";

            $response = $this->googleGet(
                $account,
                $url,
                [
                    'pageSize' => 100,
                    'orderBy' => 'createTime ASC',
                ]
            );

            $messages = [];

            foreach (($response['messages'] ?? []) as $message) {

                $sender = $message['sender'] ?? [];

                /*
                |--------------------------------------------------------------------------
                | Resolve sender name
                |--------------------------------------------------------------------------
                */

                $senderInfo = $this->resolveUserInfo(
                    $account,
                    $sender
                );

                $messages[] = [
                    'id' => $message['name'] ?? null,

                    'text' => $message['text'] ?? '',

                    'create_time' =>
                        $message['createTime'] ?? null,

                    'formatted_time' =>
                        !empty($message['createTime'])
                            ? date(
                                'd M Y, h:i A',
                                strtotime($message['createTime'])
                            )
                            : null,

                    'sender' => [
                        'name' =>
                            $sender['name'] ?? null,

                        'display_name' =>
                            $senderInfo['display_name'],

                        'email' =>
                            $senderInfo['email'],

                        'photo' =>
                            $senderInfo['photo'],

                        'type' =>
                            $sender['type'] ?? null,
                    ],

                    'thread' => [
                        'name' =>
                            $message['thread']['name'] ?? null,
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'next_page_token' =>
                    $response['nextPageToken'] ?? null,
            ]);

        } catch (\Throwable $e) {

            Log::error('Google Chat messages failed', [
                'user_id' => auth()->id(),
                'space' => $space,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to load messages.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Send Message
    |--------------------------------------------------------------------------
    */

    public function sendMessage(Request $request)
    {
        $request->validate([
            'space' => [
                'required',
                'string',
            ],

            'text' => [
                'required',
                'string',
                'max:4000',
            ],
        ]);

        $account = $this->getGoogleAccount();

        if (!$account) {
            return response()->json([
                'success' => false,
                'error' => 'Google account is not connected.',
            ], 401);
        }

        try {

            $account = $this->refreshTokenIfNeeded($account);

            $space = $request->input('space');

            if (!str_starts_with($space, 'spaces/')) {
                $space = 'spaces/' . $space;
            }

            $url =
                "https://chat.googleapis.com/v1/{$space}/messages";

            $response = $this->googlePost(
                $account,
                $url,
                [
                    'text' => $request->input('text'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => $response,
            ]);

        } catch (\Throwable $e) {

            Log::error('Google Chat send message failed', [
                'user_id' => auth()->id(),
                'space' => $request->input('space'),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to send message.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Spaces
    |--------------------------------------------------------------------------
    */

    private function getSpaces(GoogleAccount $account): array
    {
        $response = $this->googleGet(
            $account,
            'https://chat.googleapis.com/v1/spaces',
            [
                'pageSize' => 100,
            ]
        );

        $spaces = [];

        foreach (($response['spaces'] ?? []) as $space) {

            $spaceName = $space['name'] ?? null;

            if (!$spaceName) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Get members
            |--------------------------------------------------------------------------
            */

            $members = $this->getSpaceMembers(
                $account,
                $spaceName
            );

            /*
            |--------------------------------------------------------------------------
            | Resolve friendly name
            |--------------------------------------------------------------------------
            */

            $displayName = $this->resolveSpaceName(
                $space,
                $members,
                $account
            );

            $spaces[] = [
                'name' => $spaceName,

                'id' => str_replace(
                    'spaces/',
                    '',
                    $spaceName
                ),

                'display_name' => $displayName,

                'type' =>
                    $space['type'] ?? null,

                'space_type' =>
                    $space['spaceType'] ?? null,

                'space_uri' =>
                    $space['spaceUri'] ?? null,

                'threading_state' =>
                    $space['spaceThreadingState'] ?? null,

                'history_state' =>
                    $space['spaceHistoryState'] ?? null,

                'members_count' =>
                    $space['membershipCount']
                        ['joinedDirectHumanCount']
                    ?? count($members),

                'members' => $members,

                'last_active_time' =>
                    $space['lastActiveTime'] ?? null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Latest conversations first
        |--------------------------------------------------------------------------
        */

        usort(
            $spaces,
            function ($a, $b) {

                return strcmp(
                    $b['last_active_time'] ?? '',
                    $a['last_active_time'] ?? ''
                );
            }
        );

        return $spaces;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Space Members
    |--------------------------------------------------------------------------
    */

    private function getSpaceMembers(
        GoogleAccount $account,
        string $spaceName
    ): array {

        try {

            $response = $this->googleGet(
                $account,
                "https://chat.googleapis.com/v1/{$spaceName}/members",
                [
                    'pageSize' => 100,
                ]
            );

            $members = [];

            foreach (
                ($response['memberships'] ?? [])
                as $membership
            ) {

                $member =
                    $membership['member'] ?? [];

                if (
                    ($member['type'] ?? null)
                    !== 'HUMAN'
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Resolve actual Google profile
                |--------------------------------------------------------------------------
                */

                $userInfo = $this->resolveUserInfo(
                    $account,
                    $member
                );

                $members[] = [
                    'name' =>
                        $member['name'] ?? null,

                    'display_name' =>
                        $userInfo['display_name'],

                    'email' =>
                        $userInfo['email'],

                    'photo' =>
                        $userInfo['photo'],

                    'type' =>
                        $member['type'] ?? null,

                    'role' =>
                        $membership['role'] ?? null,

                    'state' =>
                        $membership['state'] ?? null,
                ];
            }

            return $members;

        } catch (\Throwable $e) {

            Log::warning(
                'Google Chat members failed',
                [
                    'user_id' => auth()->id(),
                    'space' => $spaceName,
                    'message' => $e->getMessage(),
                ]
            );

            return [];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve User Information
    |--------------------------------------------------------------------------
    |
    | Google Chat may return only:
    |
    | users/123456789
    |
    | Instead of the real person's name.
    |
    | We therefore query Google People API.
    |--------------------------------------------------------------------------
    */

    private function resolveUserInfo(
        GoogleAccount $account,
        array $member
    ): array {

        $fallback = [
            'display_name' =>
                $member['displayName']
                ?? $member['email']
                ?? 'Google User',

            'email' =>
                $member['email'] ?? null,

            'photo' => null,
        ];

        try {

            /*
            |--------------------------------------------------------------------------
            | Extract Google user ID
            |--------------------------------------------------------------------------
            */

            $resourceName =
                $member['name'] ?? null;

            if (!$resourceName) {
                return $fallback;
            }

            /*
            |--------------------------------------------------------------------------
            | Google People API
            |--------------------------------------------------------------------------
            */

            $response = $this->googleGet(
                $account,
                'https://people.googleapis.com/v1/' .
                $resourceName,
                [
                    'personFields' =>
                        'names,emailAddresses,photos',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Name
            |--------------------------------------------------------------------------
            */

            $displayName = null;

            if (
                !empty($response['names'][0]['displayName'])
            ) {
                $displayName =
                    $response['names'][0]['displayName'];
            }

            /*
            |--------------------------------------------------------------------------
            | Email
            |--------------------------------------------------------------------------
            */

            $email = null;

            if (
                !empty(
                    $response['emailAddresses'][0]['value']
                )
            ) {
                $email =
                    $response['emailAddresses'][0]['value'];
            }

            /*
            |--------------------------------------------------------------------------
            | Profile photo
            |--------------------------------------------------------------------------
            */

            $photo = null;

            if (
                !empty(
                    $response['photos'][0]['url']
                )
            ) {
                $photo =
                    $response['photos'][0]['url'];
            }

            return [
                'display_name' =>
                    $displayName
                    ?? $fallback['display_name'],

                'email' =>
                    $email
                    ?? $fallback['email'],

                'photo' =>
                    $photo,
            ];

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Do not break Chat if People API fails
            |--------------------------------------------------------------------------
            */

            Log::warning(
                'Google People profile lookup failed',
                [
                    'user_id' => auth()->id(),
                    'google_member' =>
                        $member['name'] ?? null,
                    'message' => $e->getMessage(),
                ]
            );

            return $fallback;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Space Name
    |--------------------------------------------------------------------------
    */

    private function resolveSpaceName(
        array $space,
        array $members,
        GoogleAccount $account
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Named room
        |--------------------------------------------------------------------------
        */

        if (!empty($space['displayName'])) {
            return $space['displayName'];
        }

        /*
        |--------------------------------------------------------------------------
        | Direct Message
        |--------------------------------------------------------------------------
        */

        $otherMembers = array_filter(
            $members,
            function ($member) use ($account) {

                if (
                    empty($member['name']) ||
                    empty($account->google_user_id)
                ) {
                    return true;
                }

                return $member['name']
                    !==
                    'users/' .
                    $account->google_user_id;
            }
        );

        $names = array_values(
            array_filter(
                array_map(
                    fn ($member) =>
                        $member['display_name']
                        ?? null,
                    $otherMembers
                )
            )
        );

        /*
        |--------------------------------------------------------------------------
        | One-to-one conversation
        |--------------------------------------------------------------------------
        */

        if (count($names) === 1) {
            return $names[0];
        }

        /*
        |--------------------------------------------------------------------------
        | Group conversation
        |--------------------------------------------------------------------------
        */

        if (count($names) > 1) {

            return implode(
                ', ',
                array_slice($names, 0, 3)
            )
            .
            (
                count($names) > 3
                    ? ' +' .
                        (count($names) - 3)
                    : ''
            );
        }

        return 'Google Chat';
    }


    /*
    |--------------------------------------------------------------------------
    | Google GET
    |--------------------------------------------------------------------------
    */

    private function googleGet(
        GoogleAccount $account,
        string $url,
        array $query = []
    ): array {

        $account =
            $this->refreshTokenIfNeeded($account);

        $response = Http::withToken(
            $account->access_token
        )
            ->acceptJson()
            ->get(
                $url,
                $query
            );

        if (!$response->successful()) {

            Log::error(
                'Google API GET failed',
                [
                    'url' => $url,
                    'status' =>
                        $response->status(),
                    'response' =>
                        $response->json(),
                ]
            );

            throw new \Exception(
                $response->json(
                    'error.message'
                )
                ??
                'Google API request failed.'
            );
        }

        return $response->json();
    }


    /*
    |--------------------------------------------------------------------------
    | Google POST
    |--------------------------------------------------------------------------
    */

    private function googlePost(
        GoogleAccount $account,
        string $url,
        array $data = []
    ): array {

        $account =
            $this->refreshTokenIfNeeded($account);

        $response = Http::withToken(
            $account->access_token
        )
            ->acceptJson()
            ->post(
                $url,
                $data
            );

        if (!$response->successful()) {

            Log::error(
                'Google API POST failed',
                [
                    'url' => $url,
                    'status' =>
                        $response->status(),
                    'response' =>
                        $response->json(),
                ]
            );

            throw new \Exception(
                $response->json(
                    'error.message'
                )
                ??
                'Google API request failed.'
            );
        }

        return $response->json();
    }


    /*
    |--------------------------------------------------------------------------
    | Refresh Google Access Token
    |--------------------------------------------------------------------------
    */

    private function refreshTokenIfNeeded(
        GoogleAccount $account
    ): GoogleAccount {

        /*
        |--------------------------------------------------------------------------
        | Token still valid
        |--------------------------------------------------------------------------
        */

        if (
            !$account->token_expires_at ||
            now()->lessThan(
                $account->token_expires_at
            )
        ) {
            return $account;
        }

        /*
        |--------------------------------------------------------------------------
        | Refresh token missing
        |--------------------------------------------------------------------------
        */

        if (!$account->refresh_token) {

            throw new \Exception(
                'Google token expired and no refresh token is available.'
            );
        }

        try {

            $client = new Client();

            $client->setClientId(
                config('google.client_id')
            );

            $client->setClientSecret(
                config('google.client_secret')
            );

            $newToken =
                $client->fetchAccessTokenWithRefreshToken(
                    $account->refresh_token
                );

            if (
                isset($newToken['error'])
            ) {

                Log::error(
                    'Google token refresh failed',
                    [
                        'user_id' =>
                            auth()->id(),

                        'error' =>
                            $newToken['error'],

                        'description' =>
                            $newToken[
                                'error_description'
                            ] ?? null,
                    ]
                );

                throw new \Exception(
                    'Google token refresh failed.'
                );
            }

            $account->update([
                'access_token' =>
                    $newToken['access_token'],

                'token_expires_at' =>
                    now()->addSeconds(
                        $newToken['expires_in']
                        ?? 3600
                    ),
            ]);

            return $account->fresh();

        } catch (\Throwable $e) {

            Log::error(
                'Google token refresh exception',
                [
                    'user_id' =>
                        auth()->id(),

                    'message' =>
                        $e->getMessage(),
                ]
            );

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Current User Google Account
    |--------------------------------------------------------------------------
    */

    private function getGoogleAccount(): ?GoogleAccount
    {
        return GoogleAccount::where(
            'user_id',
            auth()->id()
        )->first();
    }
}