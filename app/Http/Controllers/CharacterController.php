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

        return response()->json($character_appears_in);
    }
}
