<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramCallbackController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Log all query parameters for debugging
        logger('Instagram Callback Query Parameters:', $request->query());

        if (!$request->has('code')) {
            return response()->json(['error' => 'Authorization code not found'], 400);
        }

        $authCode = $request->query('code');

        $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => env('INSTAGRAM_CLIENT_ID'),
            'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('instagram.callback'),
            'code' => $authCode,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to get access token'], 400);
        }

        $data = $response->json();
        return response()->json([
            'access_token' => $data['access_token'],
            'user_id' => $data['user_id'],
        ]);
    }

}
