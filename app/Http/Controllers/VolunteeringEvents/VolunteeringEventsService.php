<?php


namespace App\Http\Controllers\VolunteeringEvents;


use App\Asset;
use App\Category;
use App\Cities;
use App\Contact;
use App\EventAsset;
use App\EventContact;
use App\EventLocation;
use App\EventRequirements;
use App\Http\Controllers\VolunteeringEvents\Transformers\VolunteeringEventsTransformer;
use App\Jobs\Emails\VolunteerAttendedToEventEmail;
use App\Jobs\Emails\VolunteerEventStatusWasUpdatedByOrganizationEmail;
use App\Jobs\Emails\VolunteerEventStatusWasUpdatedByVolunteerEmail;
use App\Jobs\Notifications\VolunteerAttendedToEvent;
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
        $events = $this->model->query();

        if ($request->has('search')) {
            $events->where('title', 'like', '%' . $request->input('search') . '%')
                ->orWhere('description', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('country')) {
            $events->whereHas('volunteeringLocation.location.country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('country') . '%');
            });
        }

            if ($request->has('city')) {
                $events->whereHas('volunteeringLocation.location', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('city') . '%');
                });
            }

        if ($request->has('category')) {
            $events->whereHas('category', function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->input('category') . '%');
            });
        }

        if ($request->has('organization')) {
            $events->whereHas('organization', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('organization') . '%');
            });
        }

        if ($request->has('virtual')) {
            $events->where('is_virtual', true);
        }

        if ($request->has('start_date')) {
            $events->where('start_date', $request->input('start_date'));
        }


        if ($request->has('duration')) {
            $events->whereHas('duration', function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->input('duration') . '%');
            });
        }

        if ($request->has('great_for')) {
            $events->whereHas('greatFor', function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->input('great_for') . '%');
            });
        }

        $events = $events->get();
        return $events->map(function ($item) {
            return $this->getByUuid($item->uuid);
        });
    }

    public function getByUuid($uuid) {

        $volunteeringEvent=$this->model->byUuid($uuid)->with([
            'organization' => function($query) {
                $query->select('id','uuid','name','description','location_id','website', 'user_id');
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
                $query->select('id','uuid' ,'event_id','driving_license','minimum_age','languages','orientation','background_check','other');
            },
            'volunteerFavorites',
            'volunteerAttendance',
            'volunteerInvitations'
        ])->first();

       // return $volunteeringEvent;
        return $this->transformer->transform($volunteeringEvent);
    }

    public function create(Request $request) {
        $volunteeringEvent = $this->model->create([
            'title' => $request['title'],
            'description' => isset($request['description']) ? $request['description'] : null,
            //'organization_id' => Organization::where('name',$request['organization'])->value('id'),
            'organization_id' => Auth::user()->organization->id,
            'category_id' => Category::where('value', $request['category']['value'])->value('id'),
            'is_virtual' => $request['is_virtual'],
            'ongoing' => 0, //e 1 ako e Happening now
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'estimated_hours' => $request['estimated_hours'],
            'average_hours_per_day' => $request['average_hours_per_day'],
            'duration_id' => Resources::where('value', $request['duration']['value'])->value('id'),
            'deadline' => $request['deadline'],
            'expired_id' => Resources::where('type','expired_type')->where('value','active')->value('id'),
            'status_id' => Resources::where('value', $request['status']['value'])->value('id'),
            'volunteers_needed' => $request['volunteers_needed'],
            'spaces_available' => $request['volunteers_needed'],
            'great_for_id' => Resources::where('value',$request['great_for']['value'])->value('id'),
            'group_size_id' => Resources::where('value',$request['group_size']['value'])->value('id'),
            'sleeping' => $request['sleeping'],
            'food' => $request['food'],
            'transport' => $request['transport'],
            'benefits' => $request['benefits'],
            'skills_needed' => (is_array($request['skills_needed']) && !empty($request['skills_needed'])) ? $request['skills_needed'] : null,
            'tags' => (is_array($request['tags']) && !empty($request['tags'])) ? json_encode($request['tags']) : null,
            'notes' => $request['notes'],
            'virtual_info' => $request['virtual_info'] ?? null
        ]);

        if (isset($request['location']) && isset($request['location']['city'])) {
            EventLocation::create([
                'event_id' => $volunteeringEvent->id,
                'location_id' => Cities::where('name', $request['location']['city'])->value('id'),
                'address' => $request['location']['address'] ?? null,
                'show_map' => $request['location']['show_map'] ?? null,
                'longitude' => $request['location']['longitude'] ?? null,
                'latitude' => $request['location']['latitude'] ?? null,
                'postal_code' => $request['location']['postal_code'] ?? null
            ]);
        }

        if (isset($request['requirements'])) {
            EventRequirements::create([
                'event_id' => $volunteeringEvent->id,
                'driving_license' => isset($request['requirements']['driving_license']) ? $request['requirements']['driving_license'] : null,
                'minimum_age' => isset($request['requirements']['min_age']) ? $request['requirements']['min_age'] : null,
                'languages' => isset($request['requirements']['languages']) ? json_encode($request['requirements']['languages']) : null,
                'orientation' => isset($request['requirements']['orientation']) ? $request['requirements']['orientation'] : null,
                'background_check' => isset($request['requirements']['background_check']) ? $request['requirements']['background_check'] : null,
                'other' => isset($request['requirements']['other']) ? json_encode($request['requirements']['other']) : null
            ]);
        }

        return [
            "message" => "Volunteering Event has been successfully created"
        ];
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
            $volunteering_event->update(['category_id' => Category::where('value', $request['category'])->value('id')]);
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
            $volunteering_event->update(['duration_id' => Resources::where('value',$request['duration'])->value('id')]);
        }

        if (isset($request['deadline'])) {
            $volunteering_event->update(['deadline' => $request['deadline']]);
        }

        if (isset($request['expired'])) {
            $volunteering_event->update(['expired_id' => Resources::where('value',$request['expired'])->value('id')]);
        }

        if (isset($request['status'])) {
            $volunteering_event->update(['status_id' => Resources::where('value', $request['status'])->value('id')]);
            if ('happening_now' == $request['status']) {
                $volunteering_event->update(['ongoing' => 1]);
            }
            if ('happening_now' != $request['status']) {
                $volunteering_event->update(['ongoing' => 0]);
            }
            if ('completed' == $request['status'] || 'canceled' == $request['status']) {
                $volunteering_event->update(['expired_id' => Resources::where('value', 'finished')->where('type', 'expired_type')->value('id')]);
            }
        }

        if (isset($request['volunteers_needed'])) {
            $volunteering_event->update(['volunteers_needed'=> $request['volunteers_needed']]);
        }

        if (isset($request['spaces_available'])) {
            $volunteering_event->update(['spaces_available' => $request['spaces_available']]);
        }

        if (isset($request['great_for'])) {
            $volunteering_event->update(['great_for_id' => Resources::where('value', $request['great_for'])->value('id')]);
        }

        if (isset($request['group_size'])) {
            $volunteering_event->update(['group_size_id' => Resources::where('value', $request['group_size'])->value('id')]);
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

        if (isset($request['virtual_info'])) {
            $volunteering_event->update(['virtual_info' => $request['virtual_info']]);
        }

        //return $volunteering_event;
//        $response=[];
//        $response['data'] = $this->transformer->transform($volunteering_event);
//        $response['data']['message'] = "Volunteering Event has been successfully updated";
//        return $response;

        return [
            "message" => "Volunteering Event has been successfully updated"
        ];

    }

    public function destroy($request) {
        $volunteering_event = $this->model->byUuid($request['uuid'])->first();
        $volunteering_event->delete();
        //DELETE CONNECTED TABLES - LOCATION, REQUIREMENTS , EVENT_ASSETS
        return [
            "message" => "Volunteering Event has been successfully deleted"
        ];
        return response()->noContent();
    }

    public function createEventAsset($request) {
        $asset = Asset::byUuid($request['asset_uuid'])->first();
        $event_id = VolunteeringEvents::where('uuid', $request['event_uuid'])->value('id');
        $event_asset = EventAsset::create([
            'asset_id' => $asset['id'],
            'event_id' => $event_id
        ]);

        $binary_data = Storage::disk('local')->get($asset->path);

        return [
            "message" => "Asset Created!"
        ];

        return transform_event_asset($event_asset, $asset);
    }

    public function deleteEventAsset($request) {
        $event_asset = EventAsset::byUuid($request['event_asset_uuid'])->first();
//        $asset = Asset::where('id', $event_asset['asset_id'])->first();
//        $asset->delete();
//        Storage::delete($asset['path']);

        $event_asset->delete();

        return [
            "message" => "Asset Deleted!"
        ];

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
        $volunteer_id = Volunteer::where('user_id',$request['volunteer_id'])->value('id');
        $volunteer_event_invitation = VolunteerEventInvitations::create([
            'event_id' => $volunteering_event['id'],
            'volunteer_id' => $volunteer_id,
            'status_id' => $volunteer_status['id'],
            'status' => $volunteer_status['description']
        ]);


        $volunteer = Volunteer::where('id', $volunteer_event_invitation['volunteer_id'])->with('user')->first();

        if ('invitation_sent' == $request['status']['value']) {
            NotificationInvitation::dispatch($volunteering_event, Auth::user(), Volunteer::find($volunteer_event_invitation['volunteer_id']));
            EmailInvitation::dispatch($volunteering_event, Auth::user(), $volunteer);
        }

        if ('request_sent' == $request['status']['value']) {
            $user = $this->model->where('id', $volunteering_event['id'])->with(['organization.user'])->first();
            $volunteer = Volunteer::find($volunteer_event_invitation['volunteer_id']);
            NotificationRequest::dispatch($volunteering_event, Auth::user(), $volunteer, $user->organization['user']['id']);
            EmailRequest::dispatch($volunteering_event, Auth::user(), $user->organization['user']);
        }


        //This will never happen
        if ('attended' == $request['status']['value']) {
            $volunteer_event_attendance = VolunteerEventAttendance::create([
                'event_id' => $volunteering_event['id'],
                'volunteer_id' => $volunteer_id
            ]);
            VolunteerAttendedToEvent::dispatch($volunteering_event, Auth::user(), $volunteer);
            VolunteerAttendedToEventEmail::dispatch($volunteering_event, Auth::user(), $volunteer);
        }


        return [
            "message" => "Volunteer Application Created"
        ];

        return $volunteer_event_invitation;

    }


    public function updateVolunteerInvitation($request) {
        $volunteer_invitation = VolunteerEventInvitations::byUuid($request['uuid'])->first();
        $volunteer_status = Resources::where('value', $request['status'])->first();

        if (isset($request['status'])) {
            $volunteer_invitation->update(['status' => $volunteer_status['description'], 'status_id' => $volunteer_status['id']]);
        }


        $volunteering_event = $this->model->where('id', $volunteer_invitation['event_id'])->first();
        if ('invitation_approved' != $request['status'] && 'invitation_rejected' != $request['status'] && 'invitation_canceled' != $request['status']) {
            $volunteer = Volunteer::where('id', $volunteer_invitation['volunteer_id'])->with('user')->first();
            NotificationStatusUpdateByOrganization::dispatch($volunteering_event, $volunteer_status, Auth::user(), $volunteer);
            VolunteerEventStatusWasUpdatedByOrganizationEmail::dispatch($volunteering_event, $volunteer_status, Auth::user(), $volunteer);
        }
        else {
            $user = $this->model->where('id', $volunteer_invitation['event_id'])->with(['organization.user'])->first();
            NotificationStatusUpdateByVolunteer::dispatch($volunteering_event, $volunteer_status, Auth::user(), $user->organization['user']['id']);
            VolunteerEventStatusWasUpdatedByVolunteerEmail::dispatch($volunteering_event, $volunteer_status, Auth::user(), $user->organization['user']);
        }

        if ('invitation_approved' == $request['status'] || 'request_approved' == $request['status']) {
            if ($volunteering_event->spaces_available){
                $num_volunteers = ($volunteering_event->spaces_available)-1;
                $volunteering_event->update(['spaces_available' => $num_volunteers]);
            }
        }

        if ('attended' == $request['status']) {
            $volunteer_event_attendance = VolunteerEventAttendance::create([
                'event_id' => $volunteer_invitation->event_id,
                'volunteer_id' => $volunteer_invitation->volunteer_id
            ]);

            $volunteer = Volunteer::where('id', $volunteer_event_attendance['volunteer_id'])->with('user')->first();
            VolunteerAttendedToEvent::dispatch($volunteering_event, Auth::user(), $volunteer);
            VolunteerAttendedToEventEmail::dispatch($volunteering_event, Auth::user(), $volunteer);
        }

        return [
            "message" => "Application Status Updated!"
        ];


        return $volunteer_invitation;
    }


    public function deleteVolunteerInvitation($request) {
        $volunteer_invitation = VolunteerEventInvitations::byUuid($request['uuid'])->first();
        $volunteer_invitation->delete();
        return response()->noContent();
    }


}
