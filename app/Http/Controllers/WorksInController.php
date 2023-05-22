<?php

namespace App\Http\Controllers;

use App\Models\People;
use App\Models\Staff;
use App\Models\Media;
use Illuminate\Http\Request;

class WorksInController extends Controller
{
    /**
     * Function to get the staff from a media
     * @param media_id
     */
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

    /**
     * Function to get a peerson from a media by their id
     * @param id
     */
    public function personWorksIn($id)
    {
        $medias = [];
        $person_work_in = Staff::where('person_id', $id)->paginate(50);

        foreach ($person_work_in as $media) {
                
            array_push($medias, Media::where('id', $media['media_id'])->first());
        }

        return response()->json($medias);
    }
}
