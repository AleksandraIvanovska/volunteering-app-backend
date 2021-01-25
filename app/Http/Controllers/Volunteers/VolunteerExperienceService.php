<?php


namespace App\Http\Controllers\Volunteers;


use App\Cities;
use App\Volunteer;
use App\VolunteerExperience;
use Illuminate\Http\Request;

class VolunteerExperienceService
{


    /**
     * VolunteerExperienceService constructor.
     */
    public function __construct(VolunteerExperience $model)
    {
        $this->model = $model;
    }

    public function create(Request $request) {
        $data=$request->all();
        $volunteer_id=Volunteer::byUuid($data['volunteer_uuid'])->value('id');

        $experienceData=$this->model->create([
            'job_title' => $data['job_title'],
            'company_name' => $data['company_name'],
            'location_id' => isset($data['city']) ? Cities::where('name',$data['city'])->value('id') : null,
            'start_date' => isset($data['start_date']) ? $data['start_date'] : null,
            'end_date' => isset($data['end_date']) ? $data['end_date'] : null,
            'volunteer_id' => $volunteer_id
        ]);

        return $experienceData;
    }

    public function update ($request) {
        $data=$request->all();
        $experience=$this->model->byUuid($data['uuid'])->first();

        if ($request->has('job_title')) {
            $experience->update(['job_title' => $data['job_title']]);
        }

        if ($request->has('company_name')) {
            $experience->update(['company_name' => $data['company_name']]);
        }

        if($request->has('city')) {
            $experience->update(['location_id' => Cities::where('name',$data['city'])->value('id')]);
        }

        if ($request->has('start_date')) {
            $experience->update(['start_date' => $data['start_date']]);
        }

        if ($request->has('end_date')) {
            $experience->update(['end_date' => $data['end_date']]);
        }

        return $experience;

    }

    public function destroy($request) {
        $experience=$this->model->byUuid($request['uuid'])->first();
        $experience->delete();
        return response()->noContent();
    }

}
