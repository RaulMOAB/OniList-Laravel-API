<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSubscribe;
use Illuminate\Http\Request;


class LibraryController extends Controller
{
    const MEDIA_STATUS = ['WATCHING', 'PLAN TO WATCH', 'COMPLETED', 'REWATCHING', 'PAUSED', 'DROPPED'];

    public function __construct()
    {
        //$this->middleware('auth:api');
    }
    public function libraryInfo(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->get(['media_id','title',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'episodes',
            'airing_status',
            'type'
        ]);

        $final_data = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id',$user->id)->where('media_id',$media->media_id)->get();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($final_data, $subscribed_media_status);
        }

        return response()->json($final_data);
    }

    public function getMediaStatus(string $user_id, string $media_id)
    {
        $status = UserSubscribe::where('user_id', $user_id)->where('media_id', $media_id)->get(['status']);

        if (!$status) {
            return response()->json(null);
        }

        return response()->json($status);
    }

    public function setMediaStatus(Request $request)
    {
        $num_rows = UserSubscribe::where('user_id', $request->user_id)
            ->where('media_id', $request->id)
            ->update(['status' => $request->status]);

        if ($num_rows === 1) {
            $success_msg = $num_rows . " rows updated.";
            return response()->json($request->status, 200); //*1 respuesta OK 0 respuesta mala           
        } else {
            $error_msg = "Error: Attempted to update " . $request->id . " but SELECT failed.";
            return response($error_msg, 204); //*1 respuesta OK 0 respuesta mala
        }
    }
}
