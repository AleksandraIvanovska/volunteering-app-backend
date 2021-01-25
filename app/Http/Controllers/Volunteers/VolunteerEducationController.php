<?php

namespace App\Http\Controllers\Volunteers;

use App\VolunteerEducation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VolunteerEducationController extends Controller
{
    /**
     * VolunteerEducationController constructor.
     */
    public function __construct(VolunteerEducationService $educationService)
    {
        $this->educationService = $educationService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->educationService->getAll($request);
    }

    public function getByUuid(Request $request,$uuid) {
        $request->merge(['uuid' => $uuid]);
        return $request;
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
            'institution_name' => 'required|string',
            'degree_name' => 'present|nullable|string',
            'major' => 'present|nullable|string',
            'start_date' => 'present|nullable|date',
            'graduation_date' => 'present|nullable|date'

        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->educationService->create($request);
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
        $request->merge(['uuid'=> $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_education,uuid',
            'institution_name' => 'filled|string',
            'degree_name' => 'sometimes|string|nullable',
            'major' => 'sometimes|string|nullable',
            'start_date' => 'sometimes|nullable|date',
            'graduation_date' => 'sometimes|nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->educationService->update($request);

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
            'uuid' => 'required|exists:volunteer_education,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->educationService->destroy($request);
    }
}
