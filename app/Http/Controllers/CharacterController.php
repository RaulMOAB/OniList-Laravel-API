<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Character;
use App\Models\CharactersAppearsIn;

class CharacterController extends Controller
{
    public function getCharacters(string $character_id)
    {
        $characters = Character::where('id', $character_id)->get();

        return response()->json($characters);
    }

    public function getCharacterAppearsMedia(string $media_id)
    {
        $character_appears_in = CharactersAppearsIn::where('media_id', $media_id)->get();

        $final_data = [];

        foreach ($character_appears_in as $character) {
            $character_data = Character::where('id', $character["character_id"])->first();
            $character_media_data = ['character' => $character, 'character_data' => $character_data];

            array_push($final_data, $character_media_data);
        }

        return response()->json($final_data);
    }
}
