<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleChatService
{
    /**
     * List spaces (chat rooms) for the authenticated user.
     *
     * @param string $token
     * @return array
     */
    public function listSpaces(string $token): array
    {
        $response = Http::withToken($token)
            ->get('https://chat.googleapis.com/v1/spaces');

        if ($response->successful()) {
            return $response->json()['spaces'] ?? [];
        }

        return [];
    }

    /**
     * List messages from a given space.
     *
     * @param string $token
     * @param string $spaceName
     * @return array
     */
    public function listMessages(string $token, string $spaceName): array
    {
        $response = Http::withToken($token)
            ->get("https://chat.googleapis.com/v1/{$spaceName}/messages");

        // dd($response->json());

        if ($response->successful()) {
            return $response->json()['messages'] ?? [];
        }

        return [];
    }

    public function getUserInfo(string $token, string $userName): array
   {
    $response = Http::withToken($token)
        ->get("https://chat.googleapis.com/v1/{$userName}");

    if ($response->successful()) {
        return $response->json();
    }

    return [];
  }

}
