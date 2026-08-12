<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Crypt;

use App\Models\SocialAccount;

use App\Models\ScheduledPost;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use App\Models\Media;

use App\Jobs\PublishInstagramPost;




class InstagramController extends Controller

{



    public function post($id)

    {

        $account = SocialAccount::findOrFail($id);

    

        $accessToken = Crypt::decryptString($account->access_token);

        $igUserId = $account->ig_business_id;

    

        $response = Http::get("https://graph.facebook.com/v19.0/{$igUserId}/media", [

            'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,like_count,comments_count',

            'access_token' => $accessToken

        ])->json();

    

        $posts = $response['data'] ?? [];



        $media = \App\Models\Media::where('user_id', auth()->id())->get();

    

        return view('instagram.post', compact('posts','account','media'));

    }

    

     public function redirect()

    {

        return Socialite::driver('facebook')

            ->scopes([

                'public_profile',

                'pages_show_list',

                'instagram_basic',

                'instagram_content_publish'

            ])

            ->redirect();

    }

    

    

    public function callback()

    {

        $facebookUser = Socialite::driver('facebook')->stateless()->user();

    

        $accessToken = $facebookUser->token;

    

        // Convert to Long Lived Token

        $longLived = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [

            'grant_type' => 'fb_exchange_token',

            'client_id' => config('services.facebook.client_id'),

            'client_secret' => config('services.facebook.client_secret'),

            'fb_exchange_token' => $accessToken,

        ])->json();

    

        $accessToken = $longLived['access_token'];

    

        // STEP 1: Get all Facebook Pages

        $pages = Http::get('https://graph.facebook.com/v19.0/me/accounts', [

            'access_token' => $accessToken

        ])->json(); 

    

        if (!isset($pages['data'])) {

            return "No Facebook pages found.";

        }

    

        foreach ($pages['data'] as $page) {

    

            $pageId = $page['id'];

    

            // STEP 2: Get Instagram Business Account

            $ig = Http::get("https://graph.facebook.com/v19.0/{$pageId}", [

                'fields' => 'instagram_business_account',

                'access_token' => $accessToken

            ])->json();

    

            if (!isset($ig['instagram_business_account']['id'])) {

                continue;

            }

    

            $igBusinessId = $ig['instagram_business_account']['id'];

    

            // STEP 3: Get Instagram Username

            $user = Http::get("https://graph.facebook.com/v19.0/{$igBusinessId}", [

                'fields' => 'username',

                'access_token' => $accessToken

            ])->json();

    

            $username = $user['username'] ?? null;

    

            // STEP 4: Store each Instagram account

            SocialAccount::updateOrCreate(

                ['ig_business_id' => $igBusinessId],

                [

                    'page_id' => $pageId,

                    'access_token' => Crypt::encryptString($accessToken),

                    'instagram_username' => $username,

                ]

            );

        }

    

        return "All Instagram Accounts Connected Successfully";

    }



    public function publish(Request $request, $id)

    {

        $account = SocialAccount::findOrFail($id); // ✅ REQUIRED



        PublishInstagramPost::dispatch(

            $account->id,

            $request->media_id,

            $request->caption

        );



        return back()->with('success', 'Post is being processed 🚀');

    }


    public function deletePost($id)
    {
        try {
             $post = ScheduledPost::where('instagram_post_id', $id)->first();
            // Get Instagram Post ID 
            $instagramPostId = $id;

            $accessToken = Crypt::decryptString($post->account->access_token); 
            // 🔥 Call Instagram API
            if ($instagramPostId) {
                $response = Http::delete("https://graph.facebook.com/v19.0/{$instagramPostId}", [
                    'access_token' => $accessToken
                ]);

                $result = $response->json();

                if (isset($result['error'])) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['error']['message']
                    ]);
                }
            }

            // 🗑️ Delete from DB
            $post->delete();

            return response()->json([
                'status' => true,
                'message' => 'Post deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}