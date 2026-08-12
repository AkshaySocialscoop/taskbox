<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\SocialAccount;
use App\Models\ScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Jobs\PublishInstagramPost;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {   
        $media = Media::where('user_id', auth()->id())
            ->with('scheduledPost')
            ->latest()
            ->get();
    
        $accounts = SocialAccount::all();
    
        // ✅ Get all scheduled posts with relations
        $scheduledPosts = ScheduledPost::with('account', 'media')
            ->latest()
            ->paginate(10);
    
        // ✅ Counts
        $totalScheduled = ScheduledPost::where('is_posted', 0)->count();
    
        $totalPosted = ScheduledPost::where('is_posted', 1)->count();
    
        $totalReels = ScheduledPost::whereHas('media', function($q){
            $q->where('type', 'video');
        })->count();
    
        $totalPosts = ScheduledPost::whereHas('media', function($q){
            $q->where('type', 'image');
        })->count();


        if (request()->ajax()) {
            return view('partials.schedule_table', compact('scheduledPosts'))->render();
        }
        
        return view('media.index', compact(
            'media',
            'accounts',
            'scheduledPosts',
            'totalScheduled',
            'totalPosted',
            'totalReels',
            'totalPosts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->validate([
            'media_file' => 'required|file|max:204800'
        ]);

        $file = $request->file('media_file');

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // remove spaces + special chars
        $cleanName = Str::slug($originalName);
        
        $filename = time().'_'.$cleanName.'.'.$file->getClientOriginalExtension();
        
        $path = Storage::disk('public')->putFileAs('media', $file, $filename);

        
        Media::create([
            'user_id' => Auth::id(),
            'file_name' => $filename,
            'file_path' => $path,
            'type' => str_contains($file->getMimeType(),'video') ? 'video' : 'image'
        ]);

        return back()->with('success','Media uploaded successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        //
    }
 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    
 
    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        if ($media->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return response()->json(['success' => true]);
    }
    
    public function schedule(Request $request)
    {
        $account = SocialAccount::findOrFail($request->account_id);
    
        $scheduleTime = Carbon::parse($request->schedule_time); 

        // ✅ Save and store in variable
        $scheduledPost = ScheduledPost::create([
            'account_id' => $account->id,
            'media_id' => $request->media_id,
            'caption' => $request->caption,
            'scheduled_at' => $scheduleTime,
            'is_posted' => 0
        ]);
    
        // ✅ Dispatch with correct params
        PublishInstagramPost::dispatch(
            $scheduledPost->id,
            $account->id,
            $request->media_id,
            $request->caption
        )->delay($scheduleTime);
    
        return back()->with('success', 'Post scheduled successfully ⏰');   
    
    }
        public function getPost($id)
    {
        $post = ScheduledPost::with('media')->findOrFail($id);
        return response()->json($post);
    }
    
    public function updatepost(Request $request, $id)
    {
        $post = ScheduledPost::findOrFail($id);
    
        $post->update([
            'scheduled_at' => $request->scheduled_at,
            'caption' => $request->caption
        ]);
     
    
        return response()->json(['success' => true]);
    }
    
    public function deletePost($id)
    {
        $post = ScheduledPost::findOrFail($id);
        $post->delete();
    
        return response()->json(['success' => true]);
    }
}
