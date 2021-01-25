<?php


namespace App\Http\Controllers\VolunteeringEvents;


use App\Asset;
use App\Category;
use App\Contact;
use App\EventAsset;
use App\EventContact;
use App\Http\Controllers\VolunteeringEvents\Transformers\VolunteeringEventsTransformer;
use App\Mail\VolunteerInvitation;
use App\Organization;
use App\Resources;
use App\Volunteer;
use App\VolunteerEventAttendance;
use App\VolunteerEventInvitations;
use App\VolunteeringEvents;
use Illuminate\Support\Facades\Auth;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use function App\Helpers\transform_event_asset;

use App\Jobs\Emails\VolunteerMadeRequest as EmailRequest;
use App\Jobs\Notifications\VolunteerMadeRequest as NotificationRequest;
use App\Jobs\Emails\VolunteerWasInvited as EmailInvitation;
use App\Jobs\Notifications\VolunteerWasInvited as NotificationInvitation;
use App\Jobs\Notifications\VolunteerEventsStatusWasUpdatedByOrganization as NotificationStatusUpdateByOrganization;
use App\Jobs\Notifications\VolunteerEventsStatusWasUpdatedByVolunteer as NotificationStatusUpdateByVolunteer;


class VolunteeringEventsService
{

    protected $model;
    private $transformer,$dispatcher;

    /**
     * VolunteeringEventsService constructor.
     */
    public function __construct(VolunteeringEvents $model, VolunteeringEventsTransformer $transformer, Dispatcher $dispatcher)
    {
        $this->model = $model;
        $this->transformer = $transformer;
        $this->dispatcher = $dispatcher;
    }

    public function getAll(Request $request) {
        return $this->model->get();
    }

    public function getByUuid(Request $request) {
        $volunteeringEvent=$this->model->byUuid($request['uuid'])->with([
            'organization' => function($query) {
                $query->select('id','uuid','name','description','location_id','website');
            },
            'category' => function($query) {
                $query->select('id','value','description');
            },
            'duration' => function($query) {
                $query->select('id','value','description');
            },
            'expiration' => function($query) {
                $query->select('id','value','description');
            },
            'status' => function($query) {
                $query->select('id','value','description');
            },
            'greatFor' => function($query) {
                $query->select('id','value','description');
            },
            'groupSize' => function($query) {
                $query->select('id','value','description');
            },
            'volunteeringLocation' => function($query) {
                $query->select('id','uuid','event_id','location_id','address','show_map','longitude','latitude','postal_code');
            },
            'volunteeringLocation.location' => function($query) {
                $query->select('id','name','state_id');
            },
            'volunteeringLocation.location.country' => function($query) {
                $query->select('id','name');
            },
            'assets',
            'contacts',
            'requirements' => function($query) {
                $query->select('id','event_id','driving_license','minimum_age','languages','orientation','background_check','other');
            },
            'volunteerFavorites',
            'volunteerAttendance',
            'volunteerInvitations'
        ])->first();

       // return $volunteeringEvent;
        return $this->transformer->transform($volunteeringEvent);
    }

    public function create(Request $request) {
       // return $request;
        $volunteeringEvent = $this->model->create([
            'title' => $request['title'],
            'description' => isset($request['description']) ? $request['description'] : null,
            'organization_id' => Organization::where('name',$request['organization'])->value('id'),
            'category_id' => Category::where('value', $request['category']['value'])->value('id'),
            'is_virtual' => $request['is_virtual'],
            'ongoing' => $request['ongoing'],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'estimated_hours' => $request['estimated_hours'],
            'average_hours_per_day' => $request['average_hours_per_day'],
            'duration_id' => Resources::where('value', $request['duration']['value'])->value('id'),
            'deadline' => $request['deadline'],
            'expired_id' => Resources::where('value',$request['expired']['value'])->value('id'),
            'status_id' => Resources::where('value',$request['status']['value'])->value('id'),
            'volunteers_needed' => $request['volunteers_needed'],
            'spaces_available' => $request['spaces_available'],
            'great_for_id' => Resources::where('value',$request['great_for']['value'])->value('id'),
            'group_size_id' => Resources::where('value',$request['group_size']['value'])->value('id'),
            'sleeping' => $request['sleeping'],
            'food' => $request['food'],
            'transport' => $request['transport'],
            'benefits' => $request['benefits'],
            'skills_needed' => (is_array($request['skills_needed']) && !empty($request['skills_needed'])) ? json_encode($request['skills_needed']) : null,
            'tags' => (is_array($request['tags']) && !empty($request['tags'])) ? json_encode($request['tags']) : null,
            'notes' => $request['notes']
        ]);

        return $volunteeringEvent;
    }

    public function update($request) {
        $volunteering_event=$this->model->byUuid($request['uuid'])->first();

        if (isset($request['title'])) {
            $volunteering_event->update(['title' => $request['title']]);
        }

        if (isset($request['description'])) {
            $volunteering_event->update(['description' => $request['description']]);
        }

        if (isset($request['organization'])) {
            $volunteering_event->update(['organization_id' => Organization::where('name', $request['organization'])->value('id')]);
        }

        if (isset($request['category'])) {
            $volunteering_event->update(['category_id' => Category::where('value', $request['category']['value'])->value('id')]);
        }

        if (isset($request['is_virtual'])) {
            $volunteering_event->update(['is_virtual' => $request['is_virtual']]);
        }

        if (isset($request['ongoing'])) {
            $volunteering_event->update(['ongoing' => $request['ongoing']]);
        }

        if (isset($request['start_date'])) {
            $volunteering_event->update(['start_date' => $request['start_date']]);
        }

        if (isset($request['end_date'])) {
            $volunteering_event->update(['end_date' => $request['end_date']]);
        }

        if (isset($request['estimated_hours'])) {
            $volunteering_event->update(['estimated_hours' => $request['estimated_hours']]);
        }

        if (isset($request['average_hours_per_day'])) {
            $volunteering_event->update(['average_hours_per_day' => $request['average_hours_per_day']]);
        }

        if (isset($request['duration'])) {
            $volunteering_event->update(['duration_id' => Resources::where('value',$request['duration']['value'])->value('id')]);
        }

        if (isset($request['deadline'])) {
            $volunteering_event->update(['deadline' => $request['deadline']]);
        }

        if (isset($request['expired'])) {
            $volunteering_event->update(['expired_id' => Resources::where('value',$request['expired']['value'])->value('id')]);
        }

        if (isset($request['status'])) {
            $volunteering_event->update(['status_id' => Resources::where('value', $request['status']['value'])->value('id')]);
        }

        if (isset($request['volunteers_needed'])) {
            $volunteering_event->update(['volunteers_needed'=> $request['volunteers_needed']]);
        }

        if (isset($request['spaces_available'])) {
            $volunteering_event->update(['spaces_available' => $request['spaces_available']]);
        }

        if (isset($request['great_for'])) {
            $volunteering_event->update(['great_for_id' => Resources::where('value', $request['great_for']['value'])->value('id')]);
        }

        if (isset($request['group_size'])) {
            $volunteering_event->update(['group_size_id' => Resources::where('value', $request['group_size']['value'])->value('id')]);
        }

        if (isset($request['sleeping'])) {
            $volunteering_event->update(['sleeping' => $request['sleeping']]);
        }

        if (isset($request['food'])) {
            $volunteering_event->update(['food' => $request['food']]);
        }

        if (isset($request['transport'])) {
            $volunteering_event->update(['transport' => $request['transport']]);
        }

        if (isset($request['benefits'])) {
            $volunteering_event->update(['benefits' => $request['benefits']]);
        }

        if (isset($request['skills_needed'])) {
            $volunteering_event->update(['skills_needed' => $request['skills_needed']]);
        }

        if (isset($request['tags'])) {
            $volunteering_event->update(['tags' => $request['tags']]);
        }

        if (isset($request['notes'])) {
            $volunteering_event->update(['notes' => $request['notes']]);
        }

        //return $volunteering_event;
        $response=[];
        $response['data'] = $this->transformer->transform($volunteering_event);
        return $response;

    }

    public function destroy($request) {
        $volunteering_event = $this->model->byUuid($request['uuid'])->first();
        $volunteering_event->delete();
        //DELETE CONNECTED TABLES - LOCATION, REQUIREMENTS , EVENT_ASSETS
        return response()->noContent();
    }

    public function createEventAsset($request) {
        $asset = Asset::byUuid($request['asset_uuid'])->first();
        $event_id = VolunteeringEvents::where('uuid', $request['event_uuid'])->value('id');
        $event_asset = EventAsset::create([
            'asset_id' => $asset['id'],
            'event_id' => $event_id
        ]);

        return transform_event_asset($event_asset, $asset);
    }

    public function deleteEventAsset($request) {
        $event_asset=EventAsset::byUuid($request['event_asset_uuid'])->first();
        $asset=Asset::where('id', $event_asset['asset_id'])->first();
        $asset->delete();
        Storage::delete($asset['path']);
        $event_asset->delete();

        return response()->noContent();
    }

    public function createEventContact($request) {
        $contact = Contact::byUuid($request['contact_uuid'])->first();
        $event_id = $this->model->where('uuid', $request['event_uuid'])->value('id');

        $event_contact = EventContact::create([
            'event_id' => $event_id,
            'contact_id' => $contact['id']
        ]);
        return $event_contact;
    }

    public function deleteEventContact($request) {
        $event_contact = EventContact::byUuid($request['event_contact_uuid'])->first();
        $event_contact->delete();
        return response()->noContent();
    }

    public function createVolunteerAttendance($request) {
        $volunteering_event_id = $this->model->byUuid($request['event_uuid'])->value('id');
        $volunteer_event_attendance = VolunteerEventAttendance::create([
            'event_id' => $volunteering_event_id,
            'volunteer_id' => $request['volunteer_id']
        ]);

        return $volunteer_event_attendance;
    }

    public function deleteVolunteerAttendance($request) {
        $volunteer_event_attendance = VolunteerEventAttendance::byUuid($request['uuid'])->first();
        $volunteer_event_attendance->delete();
        return response()->noContent();
    }

    public function createVolunteerInvitation($request) {
        $volunteering_event = $this->model->byUuid($request['event_uuid'])->first();
        $volunteer_status = Resources::where('value', $request['status']['value'])->first();
      //  return $status;
        $volunteer_event_invitation = VolunteerEventInvitations::create([
            'event_id' => $volunteering_event['id'],
            'volunteer_id' => $request['volunteer_id'],
            'status_id' => $volunteer_status['id'],
            'status' => $volunteer_status['description']
        ]);

        //SEND EMAIL AND NOTIFICATION
        //IF VOLUNTEER REQUESTS // IF ORGANIZATION INVITES HIM ??????????
        //IFS FOR EVERY STATUS
        //OR HERE WE CAN HAVE ONLY INVITATION SENT AND REQUEST SENT ??

        //only organizations can do this (check when making frontend)
        if ('invitation_sent' == $request['status']['value']) {
            NotificationInvitation::dispatch($volunteering_event, Auth::user(), Volunteer::find($volunteer_event_invitation['volunteer_id']));
           // $user = Volunteer::where('id', $volunteer_event_invitation['volunteer_id'])->with(['user'])->first();
            //$email=$user->user['email'];
           // Mail::to($email)->send(new VolunteerInvitation($volunteer_event_invitation,Auth::user()));
        }

        //only volunteers can do this
        if ('request_sent' == $request['status']['value']) {
            $user = $this->model->where('id', $volunteering_event['id'])->with(['organization.user'])->first();
            NotificationRequest::dispatch($volunteering_event, Auth::user(), Volunteer::find($volunteer_event_invitation['volunteer_id']), $user->organization['user']['id']);
        }


//        if ('attended' == $request['status']['value']) {
//            $volunteer_event_attendance = VolunteerEventAttendance::create([
//                'event_id' => $volunteering_event['id'],
//                'volunteer_id' => $request['volunteer_id']
//            ]);
//            //SEND EMAIL AND NOTIFICATION
//        }


        return $volunteer_event_invitation;

    }


    public function updateVolunteerInvitation($request) {
        $volunteer_invitation = VolunteerEventInvitations::byUuid($request['uuid'])->first();
        $volunteer_status = Resources::where('value', $request['status']['value'])->first();

        if (isset($request['status'])) {
            $volunteer_invitation->update(['status' => $volunteer_status['description'], 'status_id' => $volunteer_status['id']]);
        }

        //SEND MAIL AND NOTIFICATION AFTER CHANGE


        //IF APPROVED BY VOLUNTEER SEND  TO ORGANIZATION
        $volunteering_event = $this->model->where('id', $volunteer_invitation['event_id'])->first();
        if ('invitation_approved' != $request['status']['value'] && 'invitation_rejected' != $request['status']['value'] && 'invitation_canceled' != $request['status']['value']) {
            NotificationStatusUpdateByOrganization::dispatch($volunteering_event, $volunteer_status, Auth::user(), Volunteer::find($volunteer_invitation['volunteer_id']));
        }
        else {
            $user = $this->model->where('id', $volunteer_invitation['event_id'])->with(['organization.user'])->first();
            NotificationStatusUpdateByVolunteer::dispatch($volunteering_event, $volunteer_status, Auth::user(), $user->organization['user']['id']);
        }


        //IF ATTENDED CALL ANOTHER ROUTE OR CREATE NEW RECORD HERE
        if ('attended' == $request['status']['value']) {
            $volunteer_event_attendance = VolunteerEventAttendance::create([
                'event_id' => $volunteer_invitation->event_id,
                'volunteer_id' => $volunteer_invitation->volunteer_id
            ]);

            //SEND EMAIL AND NOTIFICATION
        }


        return $volunteer_invitation;
    }


    public function deleteVolunteerInvitation($request) {
        $volunteer_invitation = VolunteerEventInvitations::byUuid($request['uuid'])->first();
        $volunteer_invitation->delete();
        return response()->noContent();
    }



}
