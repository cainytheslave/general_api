<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/song', function (Request $request) {

    $response = Http::asForm()
        ->withOptions(['verify' => app()->isProduction()])
        ->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => env('SPOTIFY_REFRESH_TOKEN'),
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        ]);

    if ($response->failed()) {
        return response()->json([
            'status' => $response->status(),
            'message' => 'Error while getting access token from Spotify.'
        ]);
    }

    $response = Http::withToken($response['access_token'])
        ->withOptions(['verify' => app()->isProduction()])
        ->get('https://api.spotify.com/v1/me/player/currently-playing');

    if ($response->failed()) {
        return response()->json([
            'status' => $response->status(),
            'message' => 'Error while getting current song from Spotify.'
        ]);
    }

    if ($response->object() == null) {
        return response()->json([
            'status' => $response->status(),
            'message' => 'Currently I am not listening to Spotify.'
        ]);
    }

    $item = $response->object()->item;

    $artists = [];

    foreach ($item->artists as $a) {
        array_push($artists, $a->name);
    }

    return response()->json([
        'status' => 200,
        'song' => $item->name,
        'id' => $item->id,
        'artists' => $artists,
        'album' => $item->album->name
    ]);
});
