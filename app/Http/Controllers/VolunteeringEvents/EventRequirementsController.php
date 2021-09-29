<?php

namespace App\Http\Controllers\VolunteeringEvents;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class EventRequirementsController extends Controller
{
    /**
     * EventRequirementsController constructor.
     */
    public function __construct(EventRequirementsService $requirementsService)
    {
        $this->requirementsService = $requirementsService;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'event_uuid' => 'required|exists:volunteering_events,uuid',
            'driving_license' => 'sometimes|nullable|string',
            'minimum_age' => 'sometimes|integer|nullable',
            'languages' => 'sometimes|nullable',
            'orientation' => 'sometimes|string|nullable',
            'background_check' => 'sometimes|boolean|nullable',
            'other' => 'sometimes|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->requirementsService->create($request);
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
        $validator= Validator::make($request->all(),[
            'uuid' => 'required|exists:event_requirements,uuid',
            'driving_license' => 'sometimes|nullable|string',
            'minimum_age' => 'sometimes|integer|nullable',
            'languages' => 'sometimes|nullable',
            'orientation' => 'sometimes|string|nullable',
            'background_check' => 'sometimes|boolean|nullable',
            'other' => 'sometimes|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->requirementsService->update($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $uuid)
    {
        $request->merge(['uuid' => $uuid]);
        $validator = Validator::make($request->all(),[
           'uuid' => 'required|exists:event_requirements,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->requirementsService->destroy($request);
    }
}
