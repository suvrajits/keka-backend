<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use App\Models\User;

class InstagramController extends Controller
{
    /**
     * Upload and publish video to Instagram.
     */
    public function uploadVideo(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_token' => 'required',
            'instagram_access_token' => 'required',
            'video_url' => 'required|url',
            'caption' => 'required|string',
        ]);

        $idToken = $request->id_token;
        $instagramAccessToken = $request->instagram_access_token;

        try {
            // ✅ Step 1: Verify Google ID Token
            $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
            $publicKeys = JWK::parseKeySet($jwkResponse->json());
            $decoded = JWT::decode($idToken, $publicKeys);
            $googleId = $decoded->sub;

            // ✅ Step 2: Check if user exists
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // ✅ Step 3: Validate Instagram Access Token
            $tokenInfoResponse = Http::withToken($instagramAccessToken)
                ->get('https://graph.facebook.com/me?fields=id,username');

            if ($tokenInfoResponse->failed()) {
                return response()->json(['error' => 'Invalid Instagram Access Token'], 401);
            }

            $instagramUser = $tokenInfoResponse->json();

            // ✅ Step 4: Upload Video to Instagram
            $uploadResponse = Http::withToken($instagramAccessToken)
                ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media", [
                    'media_type' => 'VIDEO',
                    'video_url' => $request->video_url,
                    'caption' => $request->caption,
                ]);

            if ($uploadResponse->failed()) {
                return response()->json(['error' => 'Failed to upload video'], 500);
            }

            $mediaId = $uploadResponse->json()['id'];

            // ✅ Step 5: Publish the Video
            $publishResponse = Http::withToken($instagramAccessToken)
                ->post("https://graph.facebook.com/v17.0/{$instagramUser['id']}/media_publish", [
                    'creation_id' => $mediaId,
                ]);

            if ($publishResponse->failed()) {
                return response()->json(['error' => 'Failed to publish video'], 500);
            }

            return response()->json(['message' => 'Video uploaded and published successfully']);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json(['error' => 'Invalid token signature'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
