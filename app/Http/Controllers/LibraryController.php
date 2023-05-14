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


    public function animelistStats(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_anime = $user->medias()->where('type', 'ANIME')->get();

        $status_distribution = [];
        $genres = [];
        $formats = [];
        $years = [];

        foreach ($subscribed_anime as $anime) {
            $start_date_splitted = explode("-", $anime->start_date) ?? null;
            if ($start_date_splitted) {
                $year = $start_date_splitted[0];
                array_push($years, $year);
            }
            array_push($years,$anime->season_year);
            array_push($formats, $anime->format);
            $anime_genres = json_decode($anime->genres);
            foreach ( $anime_genres as $genre) {
                if($genre){
                    array_push($genres, $genre);
                }
            }
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $anime->id)->first();
            array_push($status_distribution, $status->status);
        }
        sort($years);
        $years_result = array_count_values($years);
        $years_data = array_values($years_result);
        $years_labels = array_keys($years_result);

        $formats_result = array_count_values($formats);
        $format_data = array_values($formats_result);
        $format_labels = array_keys($formats_result);

        $genres_result = array_count_values($genres);
        $genre_data = array_values($genres_result);
        $genres_labels = array_keys($genres_result);

        // Obtener números de repeticiones
        $status_results = array_count_values($status_distribution); //return counted values and each keys
        $status_data = array_values($status_results); //return array only with the values
        $status_labels = array_keys($status_results); //return array with the keys


        $animelist_data = [
            'labels_years' => $years_labels,
            'data_years' => $years_data,
            'labels_formats'=> $format_labels,
            'data_formats'=> $format_data,
            'labels_genres' => $genres_labels,
            'data_genres' => $genre_data,
            'labels_status' => $status_labels,
            'data_status' => count($status_data) > 3 ? $status_data : null
        ];
        return response()->json($animelist_data);

    }

    public function mangalistStats(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_anime = $user->medias()->where('type', 'MANGA')->get();

        $status_distribution = [];
        $genres = [];
        $formats = [];
        $years = [];

        foreach ($subscribed_anime as $manga) {

            $start_date_splitted = explode("-",$manga->start_date) ?? null;
            if($start_date_splitted){
                $year = $start_date_splitted[0];
                array_push($years, $year);
            }
            array_push($formats, $manga->format);
            $manga_genres = json_decode($manga->genres);
            foreach ($manga_genres as $genre) {
                if ($genre) {
                    array_push($genres, $genre);
                }
            }
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $manga->id)->first();
            array_push($status_distribution, $status->status);
        }
        sort($years);
        $years_result = array_count_values($years);
        $years_data = array_values($years_result);
        $years_labels = array_keys($years_result);

        $formats_result = array_count_values($formats);
        $format_data = array_values($formats_result);
        $format_labels = array_keys($formats_result);

        $genres_result = array_count_values($genres);
        $genre_data = array_values($genres_result);
        $genres_labels = array_keys($genres_result);

        // Obtener números de repeticiones
        $status_results = array_count_values($status_distribution); //return counted values and each keys
        $status_data = array_values($status_results); //return array only with the values
        $status_labels = array_keys($status_results); //return array with the keys


        $mangalist_data = [
            'labels_years' => $years_labels,
            'data_years' => $years_data,
            'labels_formats' => $format_labels,
            'data_formats' => $format_data,
            'labels_genres' => $genres_labels,
            'data_genres' => $genre_data,
            'labels_status' => $status_labels,
            'data_status' => count($status_data) > 3 ? $status_data : null
        ];
        return response()->json($mangalist_data);

    }


    public function overviewStats(string $username){
        $user = User::where('username', $username)->first();
        $subscribed_anime = $user->medias()->where('type', 'ANIME')->get([
            'media_id',
            'format',
            'genres'
        ]);
        $subscribed_manga = $user->medias()->where('type', 'MANGA')->get([
            'media_id',
            'format',
            'genres'
        ]);
        $total_animes = count($subscribed_anime);
        $total_episodes_watched = 0;

        $total_mangas = count($subscribed_manga);
        $total_chapters_readed = 0;

        $status_distribution = [];

        foreach ($subscribed_anime as $anime) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $anime->media_id)->first();
            $total_episodes_watched = $total_episodes_watched + $status->progress ?? 0;
            array_push($status_distribution, $status->status);
        }
        foreach ($subscribed_manga as $manga) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $manga->media_id)->first();
            $total_chapters_readed = $total_chapters_readed + $status->progress;
            $manga_status = "";

            if($status->status === "WATCHING"){
                $manga_status = "READING";
            }elseif($status->status === "REWATCHING"){
                $manga_status = "REREADING";
            }elseif($status->status === "PLAN TO WATCH"){
                $manga_status = "PLAN TO READ";
            }else{
                $manga_status = $status->status;
            }
            array_push($status_distribution, $manga_status);
        }

        // Obtener números de repeticiones
        $status_results = array_count_values($status_distribution);//return counted values and each keys

        $status_data = array_values($status_results); //return array only eith the values
        $current_status = array_keys($status_results);//return array with the keys

        $overview_data = [
            'total_animes'=> $total_animes,
            'total_episodes_watched' => $total_episodes_watched,
            'time_watched' => ($total_episodes_watched*24)/60, //in hours
            'total_mangas'=> $total_mangas,
            'total_chapters_readed' => $total_chapters_readed,
            'labels'=> $current_status,
            'data'=> count($status_data) > 3 ? $status_data : null
        ];
        return response()->json($overview_data);

    }
    public function animeList(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->where('type', 'ANIME')->get([
            'media_id',
            'title',
            'type',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'format',
            'genres',
            'airing_status',
            'episodes',
            'airing_status',
        ]);

        $anime_list_with_status = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->first();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($anime_list_with_status, $subscribed_media_status);
        }

        return response()->json($anime_list_with_status);
    }
    public function mangaList(string $username)
    {
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->where('type', 'MANGA')->get([
            'media_id',
            'title',
            'type',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
            'banner_image',
            'format',
            'genres',
            'airing_status',
            'episodes',
            'airing_status',
        ]);

        $anime_list_with_status = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->first();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($anime_list_with_status, $subscribed_media_status);
        }

        return response()->json($anime_list_with_status);
    }
    public function favoritesMedias(string $username){
        $user = User::where('username', $username)->first();
        $subscribed_media = $user->medias()->get([
            'media_id',
            'title',
            'type',
            'extra_large_cover_image',
            'large_cover_image',
            'medium_cover_image',
        ]);
        $favorite_media_list_with_status = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->where('favorite',1)->get();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($favorite_media_list_with_status, $subscribed_media_status);
        }

        return response()->json($favorite_media_list_with_status);
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
        ]);

        $final_data = [];

        foreach ($subscribed_media as $media) {
            $status = UserSubscribe::where('user_id', $user->id)->where('media_id', $media->media_id)->get();
            $subscribed_media_status = ['media' => $media, 'status' => $status];
            array_push($final_data, $subscribed_media_status);
        }

        return response()->json($final_data);
    }


    // public function getMediaData(string $user_id )
    // {

    // }
    public function getMediaStatus(string $user_id, string $media_id)
    {
        $status = UserSubscribe::where('user_id', $user_id)->where('media_id', $media_id)->first();

        if (!$status) {
            return response()->json(null);
        }

        return response()->json($status);
    }

    public function setMediaStatus(Request $request)
    {
        $media_status = UserSubscribe::updateOrCreate(
            ['user_id' => $request->user_id, 'media_id' => $request->media_id],
            ['status' => $request->status, 'favorite' => $request->favorite]
        );

        if ($media_status) {
            $success_msg = $media_status . " rows updated.";
            return response()->json(["message" => $success_msg]); //*1 respuesta OK 0 respuesta mala           
        } else {
            $error_msg = "Error: Attempted to update " . $request->media_id . " but SELECT failed.";
            return response(["message" => $media_status]); //*1 respuesta OK 0 respuesta mala
        }
    }

    public function insertFavorite(Request $request)
    {

        $favorite = UserSubscribe::where('user_id', $request->user_id)->where('media_id', $request->media_id)->update(['favorite' => $request->favorite]);

        return response()->json($favorite);
    }

    public function insertOrUpdateMediaData(Request $request)
    {
        $entry = UserSubscribe::updateOrCreate(
            ['user_id' => $request->user, 'media_id' => $request->media_id],
            ['status' => $request->status, 'rate' => $request->rate, 'progress' => $request->progress, 'start_date' => $request->start_date, 'end_date' => $request->endDate, 'rewatches' => $request->rewatches, 'notes' => $request->notes, 'favorite' => $request->favorite, 'private' => $request->private],
        );

        return response()->json($entry);
    }

    public function deleteMedia($media_id)
    {

        $media = UserSubscribe::where('media_id', $media_id)->delete();

        return response()->json($media);
    }
}
