<?php

namespace App\Jobs;



use App\Models\SocialAccount;
use App\Models\ScheduledPost;

use App\Models\Media;

use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Http;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;



class PublishInstagramPost implements ShouldQueue

{

    use Dispatchable, Queueable;

    public $postId;

    public $accountId;

    public $mediaId;

    public $caption;



    // âœ… Constructor (VERY IMPORTANT)

    public function __construct($postId, $accountId, $mediaId, $caption)

    { 
    
        $this->postId = $postId;

        $this->accountId = $accountId;

        $this->mediaId = $mediaId;

        $this->caption = $caption;

    }



    public function handle()

    {

        \Log::info('JOB STARTED');



        // âœ… Now this will work

        $account = SocialAccount::findOrFail($this->accountId);



        \Log::info('Account Found: ' . $account->id);



        // ðŸ‘‰ your Instagram API logic here

         try {



        $account = SocialAccount::findOrFail($this->accountId);



        $accessToken = Crypt::decryptString($account->access_token);

        $igUserId = $account->ig_business_id;



        $media = Media::findOrFail($this->mediaId);



        $mediaUrl = url('storage/' . $media->file_path);

         \Log::info('CREATE RESPONSE:', [

                'url' => $mediaUrl, 

            ]);



        // Create container

        if ($media->type == 'image') {

            $container = Http::post("https://graph.facebook.com/v19.0/{$igUserId}/media", [

                'image_url' => $mediaUrl,

                'caption' => $this->caption,

                'access_token' => $accessToken

            ])->json();

        } else {

            $response = Http::post("https://graph.facebook.com/v19.0/{$igUserId}/media", [

                'media_type' => 'REELS',

                'video_url' => $mediaUrl,

                'share_to_feed' => true,

                'caption' => $this->caption,

                'access_token' => $accessToken

            ]);

             

            

            $container = $response->json();  

            if (!isset($container['id'])) {

                \Log::error('CREATE FAILED', $container);

                return;

            }

        }



        if (!isset($container['id'])) return;



        $creationId = $container['id'];



        // Wait processing 



        $status = 'IN_PROGRESS';

        $attempts = 0;

        

        while ($status != 'FINISHED' && $attempts < 60) {

        

            sleep(3);

        

            $statusResponse = Http::get("https://graph.facebook.com/v19.0/{$creationId}", [

                'fields' => 'status_code',

                'access_token' => $accessToken

            ])->json();

        

            \Log::info('STATUS RESPONSE', ['response' => $statusResponse]);

        

            $status = $statusResponse['status_code'] ?? 'IN_PROGRESS';

        

            \Log::info('CURRENT STATUS', ['status' => $status, 'attempt' => $attempts]);

        

            if ($status == 'ERROR') {

                \Log::error('Instagram Processing Failed', $statusResponse);

                return;

            }

        

            $attempts++;

        }

        

        if ($status != 'FINISHED') {

            \Log::error('Processing Timeout', ['final_status' => $status]);

            return;

        }

        

        // Publish

        \Log::info('Publishing media...', ['creation_id' => $creationId]);



        $publishResponse = Http::post("https://graph.facebook.com/v19.0/{$igUserId}/media_publish", [

            'creation_id' => $creationId,

            'access_token' => $accessToken

        ])->json();

        

        \Log::info('PUBLISH RESPONSE', ['response' => $publishResponse]);

        

        if (!isset($publishResponse['id'])) {

            \Log::error('PUBLISH FAILED', $publishResponse);

            return;

        }

        

        \Log::info('🎉 REEL POSTED SUCCESSFULLY', [

            'post_id' => $publishResponse['id']

        ]);

         // ✅ UPDATE AFTER SUCCESS

        $post = \App\Models\ScheduledPost::find($this->postId);

        if (!empty($publishResponse['id'])) {
            $post->update([
                'is_posted' => 1,
                'instagram_post_id' => $publishResponse['id']
            ]);

            \Log::info('✅ Updated instagram_post_id for post ID: ' . $post->instagram_post_id);
        } else {
            \Log::error('❌ Post not found: ' . $this->postId);
        } 

    } catch (\Exception $e) {

        \Log::error($e->getMessage());

    }

    }

}