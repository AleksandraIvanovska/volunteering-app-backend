<?php

namespace App\Http\Controllers\VolunteeringEvents;

use App\Asset;
use App\EventAsset;
use App\EventContact;
use App\VolunteeringEvents;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VolunteeringEventsController extends Controller
{
    /**
     * VolunteeringEventsController constructor.
     */
    public function __construct(VolunteeringEventsService $eventsService)
    {
        $this->eventService = $eventsService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->eventService->getAll($request);
    }

    public function getByUuid(Request $request,$uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteering_events,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->getByUuid($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title' => 'required|string',
            'description' => 'required|nullable|string',
            'organization' => 'required|exists:organizations,name',
            'category' => 'present|nullable|array',
            'category.value' => 'required_with:category|string|exists:categories,value',
            'category.description' => 'required_with:category|string|exists:categories,description',
            'is_virtual' => 'present|boolean|nullable',
            'ongoing' => 'present|boolean|nullable',
            'start_date' => 'present|nullable|date',
            'end_date' =>  'present|nullable|date',
            'estimated_hours' => 'present|numeric|nullable',
            'average_hours_per_day' => 'present|numeric|nullable',
            'duration' => 'present|nullable|array',
            'duration.value' => 'required_with:duration|string|exists:resources,value,type,duration_type',
            'duration.description' => 'required_with:duration|string|exists:resources,description,type,duration_type',
            'deadline' => 'present|nullable|date',
            'expired' => 'present|nullable|array',
            'expired.value' => 'required_with:expiration|string|exists:resources,value,type,expired_type',
            'expired.description' => 'required_with:expiration|string|exists:resources,description,type,expired_type',
            'status' => 'present|nullable|array',
            'status.value' => 'required_with:status|string|exists:resources,value,type,event_status_type',
            'status.description' => 'required_with:status|string|exists:resources,description,type,event_status_type',
            'volunteers_needed' => 'present|nullable|integer',
            'spaces_available' => 'present|nullable|integer|lte:volunteers_needed',
            'great_for' => 'present|nullable|array',
            'great_for.value' => 'required_with:great_for|string|exists:resources,value,type,great_for_type',
            'great_for.description' => 'required_with:great_for|string|exists:resources,description,type,great_for_type',
            'group_size' => 'present|nullable|array',
            'group_size.value' => 'required_with:group_size|string|exists:resources,value,type,group_size_type',
            'group_size.description' => 'required_with:group_size|string|exists:resources,description,type,group_size_type',
            'sleeping' => 'present|nullable|string',
            'food' => 'present|nullable|string',
            'transport' => 'present|nullable|string',
            'benefits' => 'present|nullable|string',
            'skills_needed' => 'present|nullable|array',
            'tags' => 'present|nullable|array',
            'notes' => 'present|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->create($request);
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
            'uuid' => 'required|exists:volunteering_events,uuid',
            'title' => 'filled|string',
            'description' => 'filled|string',
            'category' => 'sometimes|nullable|array',
            'category.value' => 'required_with:category|string|exists:categories,value',
            'is_virtual' => 'sometimes|boolean|nullable',
            'ongoing' => 'sometimes|boolean|nullable',
            'start_date' => 'sometimes|nullable|date',
            'end_date' =>  'sometimes|nullable|date',
            'estimated_hours' => 'sometimes|numeric|nullable',
            'average_hours_per_day' => 'sometimes|numeric|nullable',
            'duration' => 'sometimes|nullable|array',
            'duration.value' => 'required_with:duration|string|exists:resources,value,type,duration_type',
            'deadline' => 'sometimes|nullable|date',
            'expired' => 'sometimes|nullable|array',
            'expired.value' => 'required_with:expiration|string|exists:resources,value,type,expired_type',
            'status' => 'sometimes|nullable|array',
            'status.value' => 'required_with:status|string|exists:resources,value,type,event_status_type',
            'volunteers_needed' => 'sometimes|nullable|integer',
            'spaces_available' => 'sometimes|nullable|integer|lte:volunteers_needed',
            'great_for' => 'sometimes|nullable|array',
            'great_for.value' => 'required_with:great_for|string|exists:resources,value,type,great_for_type',
            'group_size' => 'sometimes|nullable|array',
            'group_size.value' => 'required_with:group_size|string|exists:resources,value,type,group_size_type',
            'sleeping' => 'sometimes|nullable|string',
            'food' => 'sometimes|nullable|string',
            'transport' => 'sometimes|nullable|string',
            'benefits' => 'sometimes|nullable|string',
            'skills_needed' => 'sometimes|nullable|array',
            'tags' => 'sometimes|nullable|array',
            'notes' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->update($request);
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
           'uuid' => 'required|exists:volunteering_events,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->destroy($request);
    }


    public function createEventAsset(Request $request, $uuid) {
        $request->merge(['event_uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'event_uuid' => 'required|exists:volunteering_events,uuid',
            'asset_uuid' => ['required','exists:assets,uuid',
                function($attribute, $value, $fail) use($request) {
                    if (isset($request['asset_uuid']) && isset($request['event_uuid'])) {
                        $asset_id=Asset::where('uuid',$request['asset_uuid'])->value('id');
                        $event_id = VolunteeringEvents::where('uuid',$request['event_uuid'])->value('id');
                        if (!empty($event_id) && !empty($asset_id)) {
                            if (EventAsset::where('event_id',$event_id)->where('asset_id',$asset_id)->exists()) {
                                return $fail('This file is already assigned to this event');
                            }
                        }
                    }
                }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->createEventAsset($request);
    }


    public function deleteEventAsset(Request $request, $event_uuid, $event_asset_uuid) {
        $request->merge(['event_uuid' => $event_uuid, 'event_asset_uuid' => $event_asset_uuid]);
        $validator=Validator::make($request->all(),[
            'event_uuid' => 'required|exists:volunteering_events,uuid',
            'event_asset_uuid' => ['required','exists:event_asset,uuid',
                function($attribute, $value ,$fail) use ($request) {
                    if(array_key_exists('event_asset_uuid',$request->all())) {
                        $event_asset=EventAsset::byUuid($request['event_asset_uuid'])->first();
                        if(isset($event_asset->uuid) && VolunteeringEvents::find($event_asset->event_id)->uuid != $request['event_uuid']) {
                            return $fail("This file is not assigned to this event");
                        }
                    }
                }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->deleteEventAsset($request);
    }


    public function createEventContact(Request $request, $uuid) {
        $request->merge(['event_uuid' => $uuid]);
        $validator = Validator::make($request->all(),[
            'event_uuid' => 'required|exists:volunteering_events,uuid',
            'contact_uuid' => 'required|exists:contacts,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->createEventContact($request);
    }

    public function deleteEventContact(Request $request, $event_uuid, $event_contact_uuid) {
        $request->merge(['event_uuid' => $event_uuid, 'event_contact_uuid' => $event_contact_uuid]);
        $validator = Validator::make($request->all(),[
           'event_uuid' => 'required|exists:volunteering_events,uuid',
           'event_contact_uuid' => ['required','exists:event_contact,uuid',
               function($attribute, $value ,$fail) use ($request) {
                    if (array_key_exists('event_contact_uuid', $request->all())) {
                        $event_contact = EventContact::byUuid($request['event_contact_uuid'])->first();
                        if (isset($event_contact['uuid']) && VolunteeringEvents::find($event_contact->event_id)['uuid'] != $request['event_uuid']) {
                            return $fail("This contact is not assigned to this event");
                        }
                    }
               }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->deleteEventContact($request);
    }

    public function createVolunteerAttendance(Request $request) {
            $validator = Validator::make($request->all(),[
                'event_uuid' => 'required|exists:volunteering_events,uuid',
                'volunteer_name' => 'required|exists:volunteers,name',
                'volunteer_id' => 'required|exists:volunteers,id'
            ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->createVolunteerAttendance($request);
    }

    public function deleteVolunteerAttendance(Request $request, $uuid) {
            $request->merge(['uuid' => $uuid]);
            $validator = Validator::make($request->all(),[
               'uuid' => 'required|exists:volunteer_event_attendance,uuid'
            ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->eventService->deleteVolunteerAttendance($request);
    }




   public function createVolunteerInvitation(Request $request) {
       $validator = Validator::make($request->all(),[
           'event_uuid' => 'required|exists:volunteering_events,uuid',
           'volunteer_name' => 'required|exists:volunteers,name',
           'volunteer_id' => 'required|exists:volunteers,id',
           'status' => 'required',
           'status.value' => 'required_with:status|exists:resources,value,type,event_volunteer_status_type'
       ]);

       if ($validator->fails()) {
           return response()->json($validator->messages(),403);
       }

       return $this->eventService->createVolunteerInvitation($request);
   }

   public function updateVolunteerInvitation(Request $request, $uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator = Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_event_invitations,uuid',
            'status' => 'required',
            'status.value' => 'required_with:status|exists:resources,value,type,event_volunteer_status_type'
        ]);
       if ($validator->fails()) {
           return response()->json($validator->messages(),403);
       }

       return $this->eventService->updateVolunteerInvitation($request);
   }

   public function deleteVolunteerInvitation(Request $request, $uuid) {
       $request->merge(['uuid' => $uuid]);
       $validator = Validator::make($request->all(),[
           'uuid' => 'required|exists:volunteer_event_invitations,uuid'
       ]);

       if ($validator->fails()) {
           return response()->json($validator->messages(),403);
       }

       return $this->eventService->deleteVolunteerInvitation($request);
   }

}
