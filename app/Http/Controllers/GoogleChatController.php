<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoogleChatController extends Controller
{
      
    public function index()
{
    // If not logged in, just render the same view with no session data
    return view('googlechat.index', [
        'id'     => session('google_id'),
        'name'   => session('google_name'),
        'email'  => session('google_email'),
        'avatar' => session('google_avatar'),
    ]);
}


   
}
