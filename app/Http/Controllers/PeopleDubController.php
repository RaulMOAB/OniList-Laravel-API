<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dubbers;
use App\Models\Character;

class PeopleDubController extends Controller
{
    
    public function personDubCharacter($id) {

        $status     = '';
        $characters = [];

        if(Dubbers::where('person_id', $id)->count() === 0)
        {
            $status = 'failed';
        }
        else
        {
            $status = 'success';
            $person_dub_character = Dubbers::where('person_id', $id)->paginate(50);
            

            foreach ($person_dub_character as $character) {
                
                array_push($characters, Character::where('id', $character['character_id'])->first());
            }
        }


        return response()->json([
            'status'     => $status,
            'characters' => $characters,
        ]);
    }
}
