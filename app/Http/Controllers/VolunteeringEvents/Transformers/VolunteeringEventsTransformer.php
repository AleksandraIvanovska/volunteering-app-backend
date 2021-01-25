<?php


namespace App\Http\Controllers\VolunteeringEvents\Transformers;


use App\Resources;

class VolunteeringEventsTransformer
{
    public function transform($volunteeringEvent) {
       // return $volunteeringEvent;
        return [
            'uuid' => $volunteeringEvent->uuid,
            'title' => $volunteeringEvent->title,
            'description' => isset($volunteeringEvent->description) ? $volunteeringEvent->description : null,
            'organization' => isset($volunteeringEvent->organization) ? $this->transformOrganization($volunteeringEvent->organization) : null,
            'category' => isset($volunteeringEvent->category) ? $volunteeringEvent->category : null,
            'is_virtual' => isset($volunteeringEvent->is_virtual) ? $volunteeringEvent->is_virtual : null,
            'ongoing' => isset($volunteeringEvent->ongoing) ? $volunteeringEvent->ongoing : null,
            'start_date' => isset($volunteeringEvent->start_date) ? $volunteeringEvent->start_date : null,
            'end_date' => isset($volunteeringEvent->end_date) ? $volunteeringEvent->end_date : null,
            'estimated_hours' => isset($volunteeringEvent->estimated_hours) ? $volunteeringEvent->estimated_hours : null,
            'average_hours_per_day' => isset($volunteeringEvent->average_hours_per_day) ? $volunteeringEvent->average_hours_per_day : null,
            'duration' => isset($volunteeringEvent->duration) ? $volunteeringEvent->duration : null,
            'deadline' => isset($volunteeringEvent->deadline) ? $volunteeringEvent->deadline : null,
            'expiration' => isset($volunteeringEvent->expiration) ?  $volunteeringEvent->expiration : null,
            'status' => isset($volunteeringEvent->status) ? $volunteeringEvent->status : null,
            'volunteers_needed' => isset($volunteeringEvent->volunteers_needed) ?  $volunteeringEvent->volunteers_needed : null,
            'spaces_available' => isset($volunteeringEvent->spaces_available) ? $volunteeringEvent->spaces_available : null,
          //  'great_for' => isset($volunteeringEvent->great_for_id) ? $this->transformGreatFor($volunteeringEvent->great_for_id) : null,
          //  'group_size' => isset($volunteeringEvent->group_size_id) ? $this->transformGroupSize($volunteeringEvent->group_size_id) : null,
            'great_for' => isset($volunteeringEvent->greatFor) ? $volunteeringEvent->greatFor : null,
            'group_size' => isset($volunteeringEvent->groupSize) ? $volunteeringEvent->groupSize : null,
            'sleeping' => isset($volunteeringEvent->sleeping) ? $volunteeringEvent->sleeping : null,
            'food' => isset($volunteeringEvent->food) ? $volunteeringEvent->food : null,
            'transport' => isset($volunteeringEvent->transport) ? $volunteeringEvent->transport : null,
            'benefits' => isset($volunteeringEvent->benefits) ?  $volunteeringEvent->benefits : null,
            'skills_needed' => isset($volunteeringEvent->skills_needed) ?  $volunteeringEvent->skills_needed : null,
            'tags' => isset($volunteeringEvent->tags) ? $volunteeringEvent->tags : null,
            'notes' => isset($volunteeringEvent->notes) ? $volunteeringEvent->notes : null,
            'created_at' => isset($volunteeringEvent->created_at) ?  $volunteeringEvent->created_at : null,
            'volunteering_location' => isset($volunteeringEvent->volunteeringLocation) ?  $volunteeringEvent->volunteeringLocation : null,
            'assets' => isset($volunteeringEvent->assets) ? $this->transformAssets($volunteeringEvent->assets) : null,
            'contacts' => isset($volunteeringEvent->contacts) ? $this->transformContacts($volunteeringEvent->contacts) : null,
            'requirements' => isset($volunteeringEvent->requirements) ? $volunteeringEvent->requirements : null,
            'favorite_to_num_of_people' => isset($volunteeringEvent->volunteerFavorites) ? $volunteeringEvent->volunteerFavorites->count() : null,
            'volunteer_attendance' => isset($volunteeringEvent->volunteerAttendance) ? $this->transformVolunteersAttendance($volunteeringEvent->volunteerAttendance) : null,
            'volunteer_invitations' => isset($volunteeringEvent->volunteerInvitations) ? $this->transformInvitedVolunteers($volunteeringEvent->volunteerInvitations) : null
        ];
    }

    public function transformOrganization($organization) {
        return [
            'uuid' => $organization->uuid,
            'title' => $organization->name,
            'description' => $organization->description,
            'website' => isset($organization->website) ? $organization->website : null,
            'location' => [
                'city_id' => $organization->location['id'],
                'city' => $organization->location['name'],
                'country' => $organization->location->country['name']
            ]
        ];
    }

    public function transformGreatFor($data) {
        $row=Resources::find($data);
        return [
            'id' => $row['id'],
            'value' => $row['value'],
            'description' => $row['description']
        ];
    }

    public function transformGroupSize($data) {
        $row=Resources::find($data);
        return [
            'id' => $row['id'],
            'value' => $row['value'],
            'description' => $row['description']
        ];
    }

    public function transformAssets($assets) {
        return $assets->map(function ($item){
           return $this->transformAsset($item);
        });
    }

    public function transformAsset($asset) {
        return [
            'uuid' => $asset->uuid,
            'url' => url('app/' . $asset['path']),
            'asset_name' => $asset->asset_name
        ];
    }

    public function transformContacts($contacts) {
        return $contacts->map(function ($item) {
            return $this->transformContact($item);
        });
    }

    public function transformContact($contact) {
        return [
            'uuid' => $contact['uuid'],
            'first_name' => $contact['first_name'],
            'middle_name' => $contact['middle_name'],
            'last_name' => $contact['last_name'],
            'name' => $contact['name'],
            'phone_number' => $contact['name'],
            'email' => $contact['email'],
            'facebook' => $contact['facebook'],
            'twitter' => $contact['twitter'],
            'linkedIn' => $contact['linkedIn'],
            'skype' => $contact['skype'],
            'dob' => $contact['dob']
        ];
    }

    public function transformInvitedVolunteers($volunteer) {
            return $volunteer->map(function ($item) {
                return $this->transformInvitedVolunteer($item);
            });
    }

    public function transformInvitedVolunteer($volunteer) {
        return [
            'name' => $volunteer['name'],
            'dob' => $volunteer['dob'],
            'email' => $volunteer->user['email'],
            'gender' => $volunteer['gender'],
            'location' => [
                'city_id' => $volunteer->location['id'],
                'city' => $volunteer->location['name'],
                'country' => $volunteer->location['country']['name']
            ],
            'status' => $volunteer->pivot->status,
            'status_id' => $volunteer->pivot->status_id
        ];
    }

    public function transformVolunteersAttendance($volunteer) {
        return $volunteer->map(function ($item) {
            return $this->transformVolunteerAttendance($item);
        });
    }

    public function transformVolunteerAttendance($volunteer) {
        return [
            'name' => $volunteer['name']
        ];
    }

}
