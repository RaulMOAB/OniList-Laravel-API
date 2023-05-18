<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\Staff;
use Illuminate\Http\Request;

class WorksInController extends Controller
{
    public function getStaff(string $media_id)
    {
        $works_in = Staff::where('media_id', $media_id)->get();

        $ppl_works_in = [];

        foreach ($works_in as $staff) {
            $staff_data = People::where('id', $staff["person_id"])->first();
            $final_data = ['staff' => $staff, 'staff_data' => $staff_data];
            array_push($ppl_works_in, $final_data);
        }

        return response()->json($ppl_works_in);
    }

    public function personWorksIn($id)
    {
        $medias = Staff::where('person_id', $id)->paginate(100);

        return response()->json($medias);
    }
}
