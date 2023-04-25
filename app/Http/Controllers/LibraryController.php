<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscribe;
use Illuminate\Support\Facades\Request;


class LibraryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }
    public function libraryInfo(string $user_id)
    {
        $user = User::find($user_id);
        $subscribed_media = $user->medias()->get(['media_id','title',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'episodes',
            'airing_status'
        ]);

        $final_data = [];
        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id',$user_id)->where('media_id',$media->media_id)->get();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($final_data, $subscribed_media_status);
        }

        return response()->json($final_data);
    }
}
