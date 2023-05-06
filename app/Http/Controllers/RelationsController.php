<?php

namespace App\Http\Controllers;

use App\Models\Relations;
use App\Models\Media;
use Illuminate\Http\Request;

class RelationsController extends Controller
{
 public function getMediasRelatedTo(string $media_id)
 {
    $medias_relation = Relations::where('media_id', $media_id)->get([
        'media_id',
        'related_media_id',
        'relationship_type'
    ]);

    $related_medias = [];

    foreach ($medias_relation as $media) {
       $related_media = Media::where('id', $media["related_media_id"])->get();//TODO add only needed fields
       $medias_related_to = ['media_relationship' => $media, 'related_media' => $related_media];
        array_push($related_medias, $medias_related_to);
    }

    return response()->json($related_medias);
 }

//  public function getMedia(string $related_media_id)
//  {
//     $media = Media::where('id', $related_media_id)->first();

//     return response()->json($media);
//  }
}
