<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dubbers;

class PeopleDubController extends Controller
{
    public function personDubCharacter($id) {

        $status = '';

        if(Dubbers::where('person_id', $id)->count() === 0)
        {
            $status = 'failed';
            $characters = '';
        }
        else
        {
            $status = 'success';
            $characters = Dubbers::where('person_id', $id)->paginate(100);
        }

        return response()->json([
            'status' => $status,
            'characters' => $characters,
        ]);
    }
}
