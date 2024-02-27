<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FavoriteController extends Controller
{
    public function index()
    {
        $api_key = config('services.tmdb.api_key');
        $user = Auth::user();
        $favorites = $user->favorites;
        $details = [];

        // return response()->json($favorites);

        foreach ($favorites as $favorite) {
            $apiUrl = "https://api.themoviedb.org/3/" . $favorite->media_type . "/" . $favorite->media_id . "?api_key=a3723e7a2a6202382dc867d512504b64";
            // $tmdb_api_key = "https://api.themoviedb.org/3/tv/236000?api_key=a3723e7a2a6202382dc867d512504b64";
            $response = Http::get($apiUrl);
            if($response->successful()) {
                $details[] = array_merge($response->json(), ['media_type'=> $favorite->media_type]); //media_typeがないのでmergeで追加
                // $details[] = array_merge($response->json(), ['media_type'=> "tv"]); 
            }
            // return response()->json($response->json());
        }
        return response()->json($details);
    }

    public function toggleFavorite(Request $request)
    {
        $validateData = $request->validate([
            'media_type'=> 'required|string',
            'media_id'=> 'required|integer'
        ]);

        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('media_type', $validateData['media_type'])
            ->where('media_id', $validateData['media_id'])
            ->first();// お気に入りがあれば取得
        
         // お気に入りが存在する場合
        if($existingFavorite){
            $existingFavorite->delete();
            return response()->json(['status'=> 'removed']);
        }else{
            // お気に入りが存在しない場合
            Favorite::create([
                'media_type'=> $validateData['media_type'],
                'media_id'=> $validateData['media_id'],
                'user_id'=> Auth::id(),
            ]);
            return response()->json(['status'=> 'added']);
        }
    }

    public function checkFavoriteStatus(Request $request)
    {
        // logger(Favorite::all());
        $validateData = $request->validate([
            'media_type'=> 'required|string',
            'media_id'=> 'required|integer'
        ]);

        // logger($request["media_type"]);
        // logger($validateData);
        $isFavorite = Favorite::where("user_id", Auth::id())
        ->where("media_type", $validateData["media_type"])
        ->where("media_id", $validateData["media_id"])
        // ->where("media_type", $validateData["media_type"])
        // ->where("media_id", $validateData["media_id"])
        ->exists();

        return response()->json($isFavorite);
        // return response()->json(true);
    }

}