<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use App\Models\User;

class InstagramController extends Controller
{
    public function uploadVideo(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_token' => 'required',
            'instagram_access_token' => 'required',
            'video' => 'required|file|mimes:mp4|max:102400', // Max 100MB
            'caption' => 'required|string',
        ]);

        // ✅ Step 1: Verify Google ID Token
        $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
        $publicKeys = JWK::parseKeySet($jwkResponse->json());
        $decoded = JWT::decode($request->id_token, $publicKeys);
        $googleId = $decoded->sub;

        // ✅ Step 2: Check if user exists
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // ✅ Step 3: Validate Instagram Access Token
        $instagramAccessToken = $request->instagram_access_token;
        $tokenInfoResponse = Http::withToken($instagramAccessToken)
            ->get('https://graph.facebook.com/me?fields=id,username');

        if ($tokenInfoResponse->failed()) {
            return response()->json(['error' => 'Invalid Instagram Access Token'], 401);
        }

        $instagramUser = $tokenInfoResponse->json();

        // ✅ Step 4: Save the uploaded video to a temporary path
        $videoPath = $request->file('video')->store('videos', 'public');

        // ✅ Step 5: Upload the video to Instagram
        $uploadResponse = Http::withToken($instagramAccessToken)
            ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media", [
                'media_type' => 'VIDEO',
                'video_url' => asset('storage/' . $videoPath),
                'caption' => $request->caption,
            ]);

        if ($uploadResponse->failed()) {
            return response()->json(['error' => 'Failed to upload video'], 500);
        }

        $mediaId = $uploadResponse->json()['id'];

        // ✅ Step 6: Publish the Video
        $publishResponse = Http::withToken($instagramAccessToken)
            ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media_publish", [
                'creation_id' => $mediaId,
            ]);

        if ($publishResponse->failed()) {
            return response()->json(['error' => 'Failed to publish video'], 500);
        }

        return response()->json(['message' => 'Video uploaded and published successfully']);
    }
}
