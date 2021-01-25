<?php


namespace App\Http\Controllers\Volunteers;


use App\Volunteer;
use App\VolunteerEducation;
use Illuminate\Http\Request;

class VolunteerEducationService
{


    /**
     * VolunteerEducationService constructor.
     */
    public function __construct(VolunteerEducation $model)
    {
        $this->model = $model;
    }

    public function getAll(Request $request) {
        $educations=$this->model->get();
        return $educations;
    }

    public function getByUuid($request) {
        return response()->noContent();
    }

    public function create($request) {
        $data=$request->all();
        $volunteer_id=Volunteer::byUuid($data['volunteer_uuid'])->value('id');


        $volunteerEducation = $this->model->create([
            'institution_name' => $data['institution_name'],
            'degree_name' => isset($data['degree_name']) ? $data['degree_name'] : null,
            'major' => isset($data['major']) ? $data['major'] : null,
            'start_date' => isset($data['start_date']) ? $data['start_date'] :  null,
            'graduation_date' => isset($data['graduation_date']) ? $data['graduation_date'] : null,
            'volunteer_id' => $volunteer_id
        ]);

        return $volunteerEducation;
    }


    public function update($request) {
        $education=$this->model->byUuid($request['uuid'])->first();

        if ($request->has('institution_name')) {
            $education->update(['institution_name' => $request['institution_name']]);
        }

        if ($request->has('degree_name')) {
            $education->update(['degree_name' => $request['degree_name']]);
        }

        if ($request->has('major')) {
            $education->update(['major' => $request['major']]);
        }

        if ($request->has('start_date')) {
            $education->update(['start_date' => $request['start_date']]);
        }

        if ($request->has('graduation_date')) {
            $education->update(['graduation_date' => $request['graduation_date']]);
        }

        return $education;
    }

    public function destroy($request) {
        $education=$this->model->byUuid($request['uuid'])->first();
        $education->delete();
        return response()->noContent();
    }

}
