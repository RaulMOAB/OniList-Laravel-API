<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\Character;
use App\Models\Dubbers;
use App\Models\People;
use App\Models\CharactersAppearsIn;

class CharacterController extends Controller
{
    /**
     * Function to get characters from db
     */
    public function getCharacters(string $character_id)
    {
        $characters = Character::where('id', $character_id)->get();

        return response()->json($characters);
    }

    /**
     * Function to get all characters who appears in a media
     * @param media_id 
     */
    public function getCharacterAppearsMedia(string $media_id)
    {
        $character_appears_in = CharactersAppearsIn::where('media_id', $media_id)->get();
        $media = Media::findOrFail($media_id);
        $type  = $media->type;
        $final_data = [];

        foreach ($character_appears_in as $character) {
            
            $character_data = Character::where('id', $character["character_id"])->first();
            $dubber         = Dubbers::where('character_id', $character["character_id"])->first();

            if ($dubber && $type == "ANIME") {
                $dubber_data = People::firstWhere('id', $dubber->person_id);
                $character_media_data = ['character' => $character, 'character_data' => $character_data, 'dubber' => $dubber, 'dubber_data' => $dubber_data];
            } else {
                $character_media_data = ['character' => $character, 'character_data' => $character_data];
            }
            array_push($final_data, $character_media_data);
        }
        return response()->json($final_data);
    }

    /**
     * Function to get characters appears in media by their cahracter id
     * @param character_id
     */
    public function getCharacterAppearsIn($id)
    {
        $character_appears_in = CharactersAppearsIn::where('character_id', $id)->get();

        return response()->json($character_appears_in);
    }
}
