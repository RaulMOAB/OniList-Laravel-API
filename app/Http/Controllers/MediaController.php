<?php

namespace App\Http\Controllers;

use App\Models\Media;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MediaController extends Controller
{
  private function getPopularMedias(string $query, string $type): array
  {
    //Call to get most popular media


    $variables = [
      "page" => 1,
      "type" => $type
    ];

    $response = Http::post('https://graphql.anilist.co', [

      'query' => $query,
      'variables' => $variables,

    ]);

    $popularity_id_medias = json_decode($response->body(), true);
    $popularity_id_array = $popularity_id_medias['data']['Page']['media'];

    $popular_medias = [];
    //Select medias by id
    foreach ($popularity_id_array as $value) {
      $id = $value['id'];
      $media = Media::where('id', $id)->get();
      if (!empty($media)) {
        # code...
        array_push($popular_medias, $media[0]);
      }
    }


    return $popular_medias;
  }
  


  /**
   * Display a listing of the resource.
   */
  public function popularAnime()
  {
    $popular_animes=Media::where('season', 'SPRING')->where('type', 'ANIME')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($popular_animes),
      'message' => 'Media successfully fetched',
      'data' => $popular_animes,
    ],200);
  }

  public function popularManga()
  {
    $popular_mangas=Media::where('season', 'SPRING')->where('type', 'MANGA')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($popular_mangas),
      'message' => 'Media successfully fetched',
      'data' => $popular_mangas,
    ],200);
  }

  public function trendingAnime()
  {
    $trending_animes = Media::where('type', 'ANIME')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($trending_animes),
      'message' => 'Media successfully fetched',
      'data' => $trending_animes,
    ],200);
  }

  public function trendingManga()
  {
    $trending_mangas = Media::where('type', 'MANGA')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($trending_mangas),
      'message' => 'Media successfully fetched',
      'data' => $trending_mangas,
    ],200);
  }

  public function upcomingAnime()
  {
    $upcoming_animes=Media::where('season_year', 2024)->where('type', 'ANIME')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($upcoming_animes),
      'message' => 'Media successfully fetched',
      'data' => $upcoming_animes,
    ],200);
  }

  public function upcomingManga()
  {
    $upcoming_mangas=Media::where('season_year', 2024)->where('type', 'MANGA')->paginate(6);

    return response()->json([
      'status' => 'success',
      'media_length' => count($upcoming_mangas),
      'message' => 'Media successfully fetched',
      'data' => $upcoming_mangas,
    ],200);
  }


  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $media = Media::find($id);

    return response()->json($media);
  }

  public function filteredMedia(Request $request){

    $media = new Media();

    $media = $media->select('*');

    if(isset($request->search)){
      $media = $media->where('title','LIKE','%'. $request->search . '%');
    }

    if(isset($request->genres)){
      $media = $media->where('genres', 'LIKE','%'. $request->genres . '%' );
    }

    if(isset($request->season_year)){
      $media = $media->where('season_year', $request->season_year );
    }

    if(isset($request->season)){
      $media = $media->where('season', $request->season );
    }

    if(isset($request->format)){
      $media = $media->where('format', $request->format );
    }

    if(isset($request->airing_status)){
      $media = $media->where('airing_status', $request->airing_status );
    }

    $media = $media->paginate(18);

    return response()->json([
      'status' => 'success',
      'media_length' => count($media),
      'message' => 'Media successfully fetched',
      'data' => $media,
    ],200);

  }

  public function topAnime() 
  {
    $top = Media::where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status' => 'success',
      'media_length' => count($top),
      'message' => 'Media successfully fetched',
      'data' => $top,
    ],200);
  }
}
