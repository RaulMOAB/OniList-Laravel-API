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
    $popular_media_query = '
        query ($type: MediaType, $page: Int){
        Page(page: $page) {
            pageInfo {
              hasNextPage
            }
            media(type: $type, sort:POPULARITY_DESC) {
              id
            }
          }
        }
        ';
    // $trending_media_query = '
    // query ($type: MediaType, $page: Int){
    // Page(page: $page) {
    //     pageInfo {
    //       hasNextPage
    //     }
    //     media(type: $type, sort: TRENDING_DESC) {
    //       id
    //     }
    //   }
    // }
    // ';
    $anime_type = 'ANIME';
    //$manga_type = 'MANGA';
    $popular_animes = $this->getPopularMedias($popular_media_query, $anime_type);
    // $popular_mangas = $this->getPopularMedias($popular_media_query, $manga_type);
    // $trending_animes = $this->getPopularMedias($trending_media_query, $anime_type);
    // $trending_mangas = $this->getPopularMedias($trending_media_query, $manga_type);

    // $popular_medias = [
    //     'anime' => $popular_animes,
    //     'manga' => $popular_mangas
    // ];
    // $trending_medias = [
    //     'anime' => $trending_animes,
    //     'manga' => $trending_mangas
    // ];

    // return $result = [
    //     'popular_medias' => $popular_medias,
    //     'trending_medias' => $trending_medias
    // ];

    return $popular_animes;
  }

  public function popularManga()
  {
    $popular_media_query = '
        query ($type: MediaType, $page: Int){
        Page(page: $page) {
            pageInfo {
              hasNextPage
            }
            media(type: $type, sort:POPULARITY_DESC) {
              id
            }
          }
        }
        ';

    $manga_type = 'MANGA';

    $popular_mangas = $this->getPopularMedias($popular_media_query, $manga_type);

    return $popular_mangas;
  }

  public function trendingAnime()
  {
    $trending_media_query = '
        query ($type: MediaType, $page: Int){
        Page(page: $page) {
            pageInfo {
              hasNextPage
            }
            media(type: $type, sort: TRENDING_DESC) {
              id
            }
          }
        }
        ';
    $anime_type = 'ANIME';

    $trending_animes = $this->getPopularMedias($trending_media_query, $anime_type);

    return $trending_animes;
  }

  public function trendingManga()
  {
    $trending_media_query = '
        query ($type: MediaType, $page: Int){
        Page(page: $page) {
            pageInfo {
              hasNextPage
            }
            media(type: $type, sort: TRENDING_DESC) {
              id
            }
          }
        }
        ';

    $manga_type = 'MANGA';

    $trending_mangas = $this->getPopularMedias($trending_media_query, $manga_type);

    return $trending_mangas;
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

    $media = $media->paginate(12);

    return response()->json([
      'status' => 'success',
      'media_length' => count($media),
      'message' => 'Media successfully fetched',
      'data' => $media,
    ],200);

  }
}
