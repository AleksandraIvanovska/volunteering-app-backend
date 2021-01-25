<?php

namespace App\Http\Controllers\Volunteers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VolunteerExperienceController extends Controller
{
    /**
     * VolunteerExperienceController constructor.
     */
    public function __construct(VolunteerExperienceService $experienceService)
    {
        $this->experienceService = $experienceService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'volunteer_uuid' => 'required|exists:volunteers,uuid',
            'job_title' => 'required|string',
            'company_name' => 'required|string',
            'city' => 'present|nullable|exists:cities,name',
            'start_date' => 'present|nullable|date',
            'end_date' => 'present|nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->experienceService->create($request);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_experience,uuid',
            'job_title' => 'filled|string',
            'company_name' => 'filled|string',
            'city' => 'sometimes|nullable|exists:cities,name',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->experienceService->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$uuid)
    {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_experience,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->experienceService->destroy($request);
    }
}
