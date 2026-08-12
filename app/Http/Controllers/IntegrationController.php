<?php

namespace App\Http\Controllers;

use App\Models\GoogleAccount;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function integrations()
    {
        $googleAccount = GoogleAccount::where('user_id', auth()->id())
            ->first();

        return view('settings.integrations', compact('googleAccount'));
    }
}
