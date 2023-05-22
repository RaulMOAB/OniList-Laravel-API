<?php

namespace App\Http\Controllers;

use App\Models\Media;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MediaController extends Controller
{

/**
 * Api call to get popular medias from anilist.com using Graphql
 * This is usefull to update our database
 * This function is commented as we decided to work on our own DB
 * NOTE: Use this function in future to get actual popular medias
 */

  //** ANIME **/

  private function getPopularMedias(string $query, string $type): array
  {
    $variables = [
      "page" => 1,
      "type" => $type
    ];

    $response = Http::post('https://graphql.anilist.co', [

      'query' => $query,
      'variables' => $variables,

    ]);

    $popularity_id_medias = json_decode($response->body(), true);
    $popularity_id_array  = $popularity_id_medias['data']['Page']['media'];
    $popular_medias = [];
    //Select medias by id

    foreach ($popularity_id_array as $value) {
      $id = $value['id'];
      $media = Media::where('id', $id)->get();
      if (!empty($media)) {
        array_push($popular_medias, $media[0]);
      }
    }
    return $popular_medias;
  }


  /**
   * Function to get pupular animes from db
   */
  public function popularAnime()
  {
    $popular_animes = Media::where('season', 'SPRING')->where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($popular_animes),
      'message'      => 'Media successfully fetched',
      'data'         => $popular_animes,
    ], 200);
  }

  /**
   * Function to get animes based on actual season
   * In this case the actual season is Spring
   */
  public function thisSeasonAnime()
  {
    $this_season_animes = Media::where('season', 'SPRING')->where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($this_season_animes),
      'message'      => 'Media successfully fetched',
      'data'         => $this_season_animes,
    ], 200);
  }

/**
 * Function to get trending animes from db
 */
  public function trendingAnime()
  {
    $trending_animes = Media::where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($trending_animes),
      'message'      => 'Media successfully fetched',
      'data'         => $trending_animes,
    ], 200);
  }

/**
 * Function to get upcoming animes from db
 */
  public function upcomingAnime()
  {
    $upcoming_animes = Media::where('season_year', 2023)->where('season', 'SUMMER')->where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($upcoming_animes),
      'message'      => 'Media successfully fetched',
      'data'         => $upcoming_animes,
    ], 200);
  }

/**
 * Function to get top animes from db
 */
  public function topAnime()
  {
    $top = Media::where('type', 'ANIME')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($top),
      'message'      => 'Media successfully fetched',
      'data'         => $top,
    ], 200);
  }

/**
 * Function to get top anime movies from db
 */
  public function topMovieAnime()
  {
    $movies = Media::where('type', 'ANIME')->where('format', 'MOVIE')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($movies),
      'message'      => 'Media successfully fetched',
      'data'         => $movies,
    ], 200);
  }

  //** MANGA **/

  /**
   * Function to get pupular mangas from db
   */
  public function popularManga()
  {
    $popular_mangas = Media::where('airing_status', 'FINISHED')->where('type', 'MANGA')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($popular_mangas),
      'message'      => 'Media successfully fetched',
      'data'          => $popular_mangas,
    ], 200);
  }

/**
 * Function to get trending manga from db
 */
  public function trendingManga()
  {
    $trending_mangas = Media::where('type', 'MANGA')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($trending_mangas),
      'message'      => 'Media successfully fetched',
      'data'         => $trending_mangas,
    ], 200);
  }

/**
 * Function to get manhwa from db
 * NOTE: Manhwa is manga fromn Korea
 */
  public function manhwaManga()
  {
    $trending_mangas = Media::where('type', 'MANGA')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($trending_mangas),
      'message'      => 'Media successfully fetched',
      'data'         => $trending_mangas,
    ], 200);
  }

/**
 * Function to get upcoming manga from db
 */
  public function upcomingManga()
  {
    $upcoming_mangas = Media::where('season_year', 2024)->where('type', 'MANGA')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($upcoming_mangas),
      'message'      => 'Media successfully fetched',
      'data'         => $upcoming_mangas,
    ], 200);
  }

/**
 * Function to get top manga from db
 */
  public function topManga()
  {
    $top = Media::where('type', 'MANGA')->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($top),
      'message'      => 'Media successfully fetched',
      'data'         => $top,
    ], 200);
  }


  /**
   * Display the specified media.
   * @param {media id}
   */
  public function show(string $id)
  {
    $media = Media::find($id);

    return response()->json($media);
  }

  //** MEDIA FILTERS **/

  /**
   * Function to get filtered anime
   */
  public function filteredMediaAnime(Request $request)
  {

    $media = new Media();

    $media = $media->select('*');

    if (isset($request->search)) {
      $media = $media->where('title', 'LIKE', '%' . $request->search . '%');
    }

    if (isset($request->genres)) {
      $media = $media->where('genres', 'LIKE', '%' . $request->genres . '%');
    }

    if (isset($request->season_year)) {
      $media = $media->where('season_year', $request->season_year);
    }

    if (isset($request->season)) {
      $media = $media->where('season', $request->season);
    }

    if (isset($request->format)) {
      $media = $media->where('format', $request->format);
    }

    if (isset($request->airing_status)) {
      $media = $media->where('airing_status', $request->airing_status);
    }

    $media = $media->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($media),
      'message'      => 'Media successfully fetched',
      'data'         => $media,
    ], 200);
  }

/**
 * Function to get filtered manga
 */
  public function filteredMediaManga(Request $request)
  {

    $media = new Media();

    $media = $media->select('*');

    if (isset($request->type)) {
      $media = $media->where('type', $request->type);
    }

    if (isset($request->search)) {
      $media = $media->where('title', 'LIKE', '%' . $request->search . '%');
    }

    if (isset($request->tags)) {
      $media = $media->where('tags', 'LIKE', '%' . $request->tags . '%');
    }

    if (isset($request->format)) {
      $media = $media->where('format', $request->format);
    }

    if (isset($request->genres)) {
      $media = $media->where('genres', 'LIKE', '%' . $request->genres . '%');
    }

    if (isset($request->airing_status)) {
      $media = $media->where('airing_status', $request->airing_status);
    }

    $media = $media->paginate(100);

    return response()->json([
      'status'       => 'success',
      'media_length' => count($media),
      'message'      => 'Media successfully fetched',
      'data'         => $media,
    ], 200);
  }

  /**
   * Function to filter media in search bar
   */
  public function filteredMedia(Request $request)
  {

    $manga = new Media();
    $anime = new Media();

    $manga = $manga->select('*');
    $anime = $anime->select('*');

    if (isset($request->search) && strlen($request->search) >= 2) {
      $anime = $anime->where('title', 'LIKE', '%' . $request->search . '%');
    }

    if (isset($request->search) && strlen($request->search) >= 2) {
      $manga = $manga->where('title', 'LIKE', '%' . $request->search . '%');
    }

    $manga = $manga->where('type', 'MANGA');
    $anime = $anime->where('type', 'LIKE', 'ANIME');

    $manga = $manga->paginate(6);
    $anime = $anime->paginate(6);

    return response()->json([
      'status'       => 'success',
      'media_length' => (count($manga) + count($manga)),
      'message'      => 'Media successfully fetched',
      'manga'        => $manga,
      'anime'        => $anime,
    ], 200);
  }
}
