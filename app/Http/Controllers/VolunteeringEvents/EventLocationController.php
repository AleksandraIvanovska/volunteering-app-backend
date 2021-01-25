<?php

namespace App\Http\Controllers\VolunteeringEvents;

use App\EventLocation;
use App\Http\Controllers\Controller;
use App\VolunteeringEvents;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EventLocationController extends Controller
{
    /**
     * EventLocationController constructor.
     */
    public function __construct(EventLocationService $locationService)
    {
        $this->locationService = $locationService;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'event_uuid' => ['required','exists:volunteering_events,uuid',
                function($attribute, $value ,$fail) use ($request) {
                     if(array_key_exists('event_uuid', $request->all())){
                         $event_id=VolunteeringEvents::byUuid($request['event_uuid'])->value('id');
                        if (!empty(EventLocation::where('event_id',$event_id)->first())) {
                            return $fail('This event already has a location');
                        }
                        }
                }
            ],
            'city' => 'required|string|exists:cities,name',
            'address' => 'present|nullable|string',
            'show_map' => 'present|nullable|boolean',
            'longitude' => 'present|nullable|numeric',
            'latitude' => 'present|nullable|numeric',
            'postal_code' => 'present|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->locationService->create($request);
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
        $validator = Validator::make($request->all(),[
            'uuid' => 'required|exists:event_location,uuid',
            'city' => 'filled|string|exists:cities,name',
            'address' => 'sometimes|nullable|string',
            'show_map' => 'sometimes|nullable|boolean',
            'longitude' => 'sometimes|nullable|numeric',
            'latitude' => 'sometimes|nullable|numeric',
            'postal_code' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->locationService->update($request);
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
        $validator = Validator::make($request->all(),[
            'uuid' => 'required|exists:event_location,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->locationService->destroy($request);
    }
}
