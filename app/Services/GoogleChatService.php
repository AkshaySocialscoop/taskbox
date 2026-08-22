<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleChatService
{
    protected const BASE_URL = 'https://chat.googleapis.com/v1';

    /**
     * Get valid Google access token from user
     */
    protected function getToken(User $user): ?string
    {
        if (! $user->google_token) {
            Log::info('No token found for user: '.$user->id);
            return null;
        }

        if ($user->google_token_expires_at && now()->gte($user->google_token_expires_at)) {
            Log::info('Token expired for user: '.$user->id);
            return null;
        }

        return $user->google_token;
    }

    /**
     * LIST SPACES
     */
    public function listSpaces(User $user, int $pageSize = 50): array
    {
        $token = $this->getToken($user);

        if (! $token) {
            Log::error('No valid token for listSpaces', ['user_id' => $user->id]);
            return [
                'success' => false,
                'message' => 'No valid token',
                'spaces' => [],
                'nextPageToken' => null,
            ];
        }

        try {
            $response = Http::withToken($token)
                ->get(self::BASE_URL.'/spaces', [
                    'pageSize' => $pageSize,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Spaces fetched successfully', [
                    'user_id' => $user->id,
                    'count' => count($data['spaces'] ?? []),
                ]);

                return [
                    'success' => true,
                    'message' => 'Spaces retrieved successfully',
                    'spaces' => $data['spaces'] ?? [],
                    'nextPageToken' => $data['nextPageToken'] ?? null,
                ];
            }

            Log::error('API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'API error: '.$response->status(),
                'spaces' => [],
                'nextPageToken' => null,
            ];

        } catch (\Exception $e) {
            Log::error('Exception in listSpaces: '.$e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception: '.$e->getMessage(),
                'spaces' => [],
                'nextPageToken' => null,
            ];
        }
    }

    /**
     * GET MESSAGES FROM SPACE
     */
    public function getMessages(User $user, string $spaceName, int $pageSize = 30): array
    {
        $token = $this->getToken($user);

        if (! $token) {
            return [
                'success' => false,
                'message' => 'No valid token',
                'messages' => [],
                'nextPageToken' => null,
            ];
        }

        try {
            $response = Http::withToken($token)
                ->get(self::BASE_URL.'/'.$spaceName.'/messages', [
                    'pageSize' => $pageSize,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'message' => 'Messages retrieved successfully',
                    'messages' => $data['messages'] ?? [],
                    'nextPageToken' => $data['nextPageToken'] ?? null,
                ];
            }

            Log::error('Failed to get messages', [
                'space' => $spaceName,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => 'API error: '.$response->status(),
                'messages' => [],
                'nextPageToken' => null,
            ];

        } catch (\Exception $e) {
            Log::error('Error getting messages: '.$e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception: '.$e->getMessage(),
                'messages' => [],
                'nextPageToken' => null,
            ];
        }
    }

    /**
     * GET SPACE MEMBERS
     */
    public function getSpaceMembers(User $user, string $spaceName): array
    {
        $token = $this->getToken($user);

        if (! $token) {
            return [];
        }

        try {
            $response = Http::withToken($token)
                ->get(self::BASE_URL.'/'.$spaceName.'/members');

            if ($response->successful()) {
                $data = $response->json();
                return $data['memberships'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Error getting members: '.$e->getMessage());
            return [];
        }
    }

    /**
     * GET USER BY EMAIL
     */
    public function getUserByEmail(User $user, string $email): ?array
    {
        $token = $this->getToken($user);

        if (! $token) {
            return null;
        }

        try {
            $response = Http::withToken($token)
                ->get(self::BASE_URL.'/users/'.$email);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error getting user: '.$e->getMessage());
            return null;
        }
    }
}