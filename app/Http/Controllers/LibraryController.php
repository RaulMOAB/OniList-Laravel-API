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
        $this->middleware('auth:api', ['except' => ['getMediaStatus', 'setMediaStatus']]);
    }

    public function animeList(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->where('type', 'ANIME')->get([
            'media_id',
            'title',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'episodes',
            'airing_status',
        ]);

        $anime_list_with_status = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->get();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($anime_list_with_status, $subscribed_media_status);
        }

        return response()->json($anime_list_with_status);
    }
    public function libraryInfo(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->get([
            'media_id',
            'title',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'episodes',
            'format',
            'airing_status',
            'genres',
            'type',
            'airing_status',
        ]);

        $final_data = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->get();
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
        // $num_rows = UserSubscribe::where('user_id', $request->user_id)
        //     ->where('media_id', $request->id)
        //     ->update(['status' => $request->status]);

        $media_status = UserSubscribe::updateOrCreate(
            ['user_id' => $request->user_id, 'media_id' => $request->media_id],
            ['status' => $request->status, 'favorite' => $request->favorite,]
        );

        if ($media_status) {
            $success_msg = $media_status . " rows updated.";
            return response()->json(["message" => $success_msg]); //*1 respuesta OK 0 respuesta mala           
        } else {
            $error_msg = "Error: Attempted to update " . $request->media_id . " but SELECT failed.";
            return response(["message" => $media_status]); //*1 respuesta OK 0 respuesta mala
        }
    }

    public function insertOrUpdateFavorite(Request $request)
    {
        $favorite = UserSubscribe::updateOrCreate(
            ['user_id' => $request->user, 'media_id' => $request->media_id],
            ['favorite' => $request->favorite]
        );
        return response()->json($favorite);
    }

    public function insertOrUpdateMediaData(Request $request)
    {
        //print_r($request);
        $entry = UserSubscribe::updateOrCreate(
            ['user_id' => $request->user, 'media_id' => $request->media_id],
            ['status' => $request->status, 'rate' => $request->rate, 'progress' => $request->progress, 'start_date' => $request->startDate, 'end_date' => $request->endDate, 'rewatches' => $request->rewatches, 'notes' => $request->notes, 'favorite' => $request->favorite, 'private' => $request->private],
        );

        return response()->json($entry);
    }

    public function deleteMedia($media_id)
    {

        $media = UserSubscribe::where('media_id', $media_id)->delete();

        return response()->json($media);
    }
}
