<?php

namespace App\Http\Controllers;

use App\Models\Dubbers;
use App\Models\People;
use App\Models\Staff;

use Illuminate\Http\Request;

class PeopleController extends Controller
{

    public function getStaffPerson(string $person_id)
    {
        $person = People::find($person_id);

        $job = Staff::where('person_id', $person_id)->first();

        return response()->json([
            'person' => $person,
            'job'    => $job,
        ]);
    }
    public function peopleDubCharacter(string $character_id)
    {
        $ppl_dubs = Dubbers::where('character_id', $character_id)->get();

        $final_data = [];

        foreach ($ppl_dubs as $person) {
            $staff_data = People::where('id', $person["person_id"])->first();
            $character_staff_data = ['person' => $person, 'staff' => $staff_data];

            array_push($final_data, $character_staff_data);
        }

        return response()->json($final_data);
    }
}
