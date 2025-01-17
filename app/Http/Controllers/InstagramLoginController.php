<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramLoginController extends Controller
{
    public function redirectToInstagram()
    {
        $url = "https://api.instagram.com/oauth/authorize?" . http_build_query([
            'client_id' => env('INSTAGRAM_CLIENT_ID'),
            'redirect_uri' => env('INSTAGRAM_REDIRECT_URI'),
            'scope' => 'user_profile,user_media',
            'response_type' => 'code',
        ]);

        return redirect($url);
    }
}
