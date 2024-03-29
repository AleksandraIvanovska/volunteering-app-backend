<?php


namespace App\Http\Controllers\Volunteers\Transformers;


use App\LanguageLevel;
use App\Roles;
use App\Volunteer;
use App\VolunteerEventAttendance;
use League\Fractal\TransformerAbstract;
use function App\Helpers\isDate;
use Carbon\Carbon;

class VolunteersTransformer extends TransformerAbstract
{

    public function transform($volunteer) {
        return [
            'uuid' => $volunteer->uuid,
            'user_id' => $volunteer->user_id,
            'email' => $volunteer->user['email'],
            'role' => $volunteer->user->role['name'],
            'first_name' => isset($volunteer->first_name) ? $volunteer->first_name : null,
            'middle_name' => isset($volunteer->middle_name) ? $volunteer->middle_name :null,
            'last_name' => isset($volunteer->last_name) ? $volunteer->last_name : null,
            'name' => $volunteer->name,
            'photo' => isset($volunteer->photo) ? $volunteer->photo : null,
            'genderType' => isset($volunteer->genderType) ? $volunteer->genderType : null,
            'nationality' => isset($volunteer->nationality) ? $volunteer->nationality : null,
            'dob' => isset($volunteer->dob) ? Carbon::parse($volunteer->dob)->format('m-d-Y') : null,
            'asset' => isset($volunteer->asset) ? $this->transformAsset($volunteer->asset) : null,
            'facebook' => isset($volunteer->facebook) ? $volunteer->facebook : null,
            'twitter' => isset($volunteer->twitter) ? $volunteer->twitter : null,
            'instagram' => isset($volunteer->instagram) ? $volunteer->instagram : null,
            'linkedIn' => isset($volunteer->linkedIn) ? $volunteer->linkedIn : null,
            'skype' => isset($volunteer->skype) ? $volunteer->skype : null,
            'phone_number' => isset($volunteer->phone_number) ? $volunteer->phone_number : null,
            'my_causes' => isset($volunteer->my_causes) ? $volunteer->my_causes : null,
            'location' => isset($volunteer->location) ? $this->transformLocation($volunteer->location) : null,
            'skills' => isset($volunteer->skills) ? $volunteer->skills : null,
            //'comments' => isset($volunteer->user->commentReceiver) ? $this->transformComments($volunteer->user->commentReceiver) : null,
            'comments' => $this->transformComments($volunteer),
            'educations' => isset($volunteer->educations) ? $this->transformEducations($volunteer->educations) : null,
            'experiences' => isset($volunteer->experiences) ? $this->transformExperiences($volunteer->experiences) : null,
            'languages' => isset($volunteer->languages) ? $this->transformLanguageLevels($volunteer->languages) : null,
            'favoriteEvents' => isset($volunteer->favoriteEvents) ? $this->transformEvents($volunteer->favoriteEvents) : null,
            'favoriteOrganizations' => isset($volunteer->favoriteOrganizations) ? $this->transformFavoriteOrganizations($volunteer->favoriteOrganizations) : null,
            'eventAttendance' => isset($volunteer->eventAttendance) ? $this->transformEvents($volunteer->eventAttendance) : null,
            'eventInvitations' => isset($volunteer->eventInvitations) ? $this->transformInvitedEvents($volunteer->eventInvitations, $volunteer->id) : null,
            'bind_name' =>  $volunteer->name . ' - ' . (isset($volunteer->location) ? $volunteer->location->name . ', ' .  $volunteer->location['country']['name'] : null)

        ];
    }

    public function transformAsset($asset) {
        return [
          'uuid' => $asset->uuid,
          'url' => url('app/' . $asset['path']),
          'asset_name' => $asset->asset_name
        ];
    }

    public function transformLocation($location) {
        return [
            'city_id' => $location['id'],
            'city' => $location['name'],
            'country' => $location['country']['name']
        ];
    }

    public function transformComments($volunteer) {
        $comments = $volunteer->user->commentReceiver;
        $response= collect();
        foreach ($comments as $comment) {
            $createdAt = Carbon::parse($comment['created_at']);
            $response->push([
                'comment_id' => $comment->id,
                'comment_uuid' => $comment->uuid,
                'title' => (($comment->creator) ? $comment->creator->name : 'Unknown user') . '<strong>  left a comment</strong> ',
                'body' => $comment->description,
                'created_date' => $createdAt->format('M d Y'),
                'creator' => ($comment->creator) ? $comment->creator->name : null,
                'creator_id' => $comment->creator_id ?? null
            ]);
        }
        return $response;

//        return $comments->map(function ($item) {
//            return $this->transformComment($item);
//        });
    }

    public function transformComment($comment) {
        return [
                'comment_id' => $comment->id,
                'title' => (($comment->creator) ? $comment->creator->name : 'Unknown user') . '<strong>  left a comment</strong> ',
                'body' => $comment->description,
                'created_date' => $comment->created_at
        ];
    }

    public function transformEducations($educations) {
        return $educations->map(function ($item) {
            return $this->transformEducation($item);
        });
    }

    public function transformEducation($education) {
        return [
            'education_id' => $education->id,
            'uuid' => $education->uuid,
            'institution_name' => isset($education->institution_name) ? $education->institution_name : null,
            'degree_name' => isset($education->degree_name) ? $education->degree_name : null,
            'major' => isset($education->major) ? $education->major : null,
            //'start_date' => isDate($education->start_date) ? Carbon::parse($education->start_date)->timestamp : null,
            'start_date' => isset($education->start_date) ? $education->start_date : null,
            'graduation_date' => isset($education->graduation_date) ? $education->graduation_date : null
        ];
    }

    public function transformExperiences($experiences) {
        return $experiences->map(function ($item) {
           return $this->transformExperience($item);
        });
    }

    public function transformExperience($experience) {
        return [
            'uuid' => $experience->uuid,
            'experience_id' => $experience->id,
            'job_title' => $experience->job_title,
            'company_name' => $experience->company_name,
            'location' => isset($experience->location) ? [
                'city_id' => $experience->location['id'],
                'city' => $experience->location['name'],
                'country' => $experience->location['country']['name']
            ] : null,
            'start_date' => isset($experience->start_date) ? $experience->start_date : null,
            'end_date' => isset($experience->end_date) ? $experience->end_date : null
        ];
    }

    public function transformLanguageLevels($languages) {
        return $languages->map(function ($item) {
            return $this->transformLanguageLevel($item);
        });
    }

    public function transformLanguageLevel($language) {
        return [
            'language_uuid' => $language->pivot->uuid,
            'language_id' => $language->id,
            'language' => $language->language,
            //'level' => LanguageLevel::where('id', $language['pivot']['language_id'])->value('description')
            'level' => $language->pivot->languageLevel['value'],
            'level_description' => $language->pivot->languageLevel['description']
        ];
    }

    public function transformEvents($events) {
        return $events->map(function ($item) {
            return $this->transformEvent($item);
        });
    }

    public function transformEvent($event) {
        return [
            'event_id' => $event->id,
            'uuid' => $event->uuid,
            'title' => $event->title,
            'description' => $event->description,
            'organization' => [
                'organization_id' => $event->organization_id,
                'organization_uuid' => $event->organization['uuid'],
                'organization_name' => $event->organization['name']
            ],
            'category' => $event->category['description'],
            'location' => isset($event->volunteeringLocation->location) ? [
                'city_id' => $event->volunteeringLocation['location']['id'],
                'city' => $event->volunteeringLocation['location']['name'],
                'country' => $event->volunteeringLocation['location']['country']['name']
            ] : null,
            'start_date' => isset($event->start_date) ? $event->start_date : null,
            'end_date' => isset($event->end_date) ? $event->end_date : null,
            'favorite_event_uuid' => $event->pivot->uuid
        ];
    }

    public function transformFavoriteOrganizations($organizations) {
        return $organizations->map(function ($item){
            return $this->transformFavoriteOrganization($item);
        });
    }

    public function transformFavoriteOrganization($organization) {
        return [
            'organization_id' => $organization->id,
            'uuid' => $organization->uuid,
            'name' => $organization->name,
            'description' => $organization->description,
            'location' => isset($organization->location) ? [
                'city_id' => $organization->location['id'],
                'city' => $organization->location['name'],
                'country' => $organization->location['country']['name']
             ] : null,
            'favorite_organization_uuid' => $organization->pivot->uuid

            ];
    }

    public function transformInvitedEvents($events, $volunteer_id) {
        return $events->map(function ($item) use ($volunteer_id) {
           if (!VolunteerEventAttendance::where('event_id', $item->id)->where('volunteer_id', $volunteer_id)->exists()) {
                return $this->transformInvitedEvent($item);
            }
        })
            ->filter()->values()->all();
    }

    public function transformInvitedEvent($event) {

        $attended = VolunteerEventAttendance::where('event_id', $event->id)->exists();


        return [
            'event_id' => $event->id,
            'uuid' => $event->uuid,
            'title' => $event->title,
            'description' => $event->description,
            'organization' => [
                'organization_id' => $event->organization_id,
                'organization_uuid' => $event->organization['uuid'],
                'organization_name' => $event->organization['name']
            ],
            'category' => $event->category['description'],
            'location' => isset($event->volunteeringLocation->location) ? [
                'city_id' => $event->volunteeringLocation['location']['id'],
                'city' => $event->volunteeringLocation['location']['name'],
                'country' => $event->volunteeringLocation['location']['country']['name']
            ] : null,
            'status' => isset($event->pivot->statusType) ? $event->pivot->statusType['description'] : null,
            'start_date' => isset($event->start_date) ? $event->start_date : null,
            'end_date' => isset($event->end_date) ? $event->end_date : null,
            'attended' => $attended ?? 0
        ];

//        $data=collect($this->transformEvent($event));
//        $data->push(['status' => $event->pivot->statusType['description']]);
////        return [
////            'status' => $event->pivot->statusType['description']
////        ];
//        return $data;
    }
}
