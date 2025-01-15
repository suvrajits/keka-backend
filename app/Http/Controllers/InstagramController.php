<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class InstagramController extends Controller
{
    public function uploadVideo(Request $request)
    {
        // ✅ Step 1: Validate the request
        $request->validate([
            'id_token' => 'required',
            'instagram_access_token' => 'required',
            'video' => 'required|file|mimes:mp4|max:102400', // Max 100MB
            'caption' => 'required|string',
        ]);

        // ✅ Step 2: Verify the Google ID token
        $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
        $publicKeys = JWK::parseKeySet($jwkResponse->json());
        $decoded = JWT::decode($request->id_token, $publicKeys);
        $googleId = $decoded->sub;

        // ✅ Step 3: Check if the user exists
        $user = User::where('google_id', $googleId)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // ✅ Step 4: Save the video file temporarily on the server
        $videoPath = $request->file('video')->store('videos', 'public');

        // ✅ Step 5: Store video metadata in the database
        $video = Video::create([
            'user_id' => $user->id,
            'caption' => $request->caption,
            'video_path' => $videoPath,
            'status' => 'pending',
        ]);

        // ✅ Step 6: Validate the Instagram access token
        $instagramAccessToken = $request->instagram_access_token;
        $tokenInfoResponse = Http::withToken($instagramAccessToken)
            ->get('https://graph.facebook.com/me?fields=id,username');
        if ($tokenInfoResponse->failed()) {
            return response()->json(['error' => 'Invalid Instagram Access Token'], 401);
        }

        $instagramUser = $tokenInfoResponse->json();

        // ✅ Step 7: Upload the video to Instagram
        $uploadResponse = Http::withToken($instagramAccessToken)
            ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media", [
                'media_type' => 'VIDEO',
                'video_url' => asset('storage/' . $videoPath),
                'caption' => $request->caption,
            ]);

        if ($uploadResponse->failed()) {
            return response()->json(['error' => 'Failed to upload video to Instagram'], 500);
        }

        $mediaId = $uploadResponse->json()['id'];

        // ✅ Step 8: Publish the video on Instagram
        $publishResponse = Http::withToken($instagramAccessToken)
            ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media_publish", [
                'creation_id' => $mediaId,
            ]);

        if ($publishResponse->failed()) {
            return response()->json(['error' => 'Failed to publish video on Instagram'], 500);
        }

        // ✅ Step 9: Update the video status in the database
        $video->update([
            'instagram_video_id' => $mediaId,
            'status' => 'uploaded',
        ]);

        // ✅ Step 10: Remove the video file from the server
        Storage::disk('public')->delete($videoPath);

        // ✅ Step 11: Return success response
        return response()->json([
            'message' => 'Video uploaded and published successfully',
            'video' => $video,
        ]);
    }
}
