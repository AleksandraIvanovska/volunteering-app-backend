<?php


namespace App\Http\Controllers\Organizations\Transformers;

use App\Countries;
use App\Organization;
use App\Roles;
use Carbon\Carbon;
use Dingo\Api\Auth\Auth;
use League\Fractal\TransformerAbstract;

class OrganizationsTransformer extends TransformerAbstract
{
    public function transform(Organization $organization) {
        return [
            'uuid' => $organization->uuid,
            'user_id' => $organization->user_id,
            'email' => $organization->user['email'],
            'role' => $organization->user->role['name'],
            'name' => $organization->name,
            'mission' => $organization->mission,
            'description' => $organization->description,
            'photo' => $organization->photo,
            'location' => $this->transformLocation($organization->location),
            'website' => $organization->website,
            'facebook'=> $organization->facebook,
            'linkedIn' => $organization->linkedIn,
            'twitter' => $organization->twitter,
            'instagram' => $organization->instagram,
            'phone_number' => $organization->phone_number,
            'comments' => $this->transformComments($organization),
            'categories' => $this->transformCategories($organization->categories),
            'assets' =>  $this->transformAssets($organization->assets),
            'volunteeringEvents' => $this->transformEvents($organization->volunteeringEvents),
            'contacts' => $this->transformContacts($organization->contacts)
        ];
    }

    public function transformContacts($contacts) {
        $response= collect();
        foreach ($contacts as $contact) {
            $dob = Carbon::parse($contact['dob']);
            $response->push([
                'contact_uuid' => $contact->uuid,
                'name' => $contact->name,
                'first_name' => $contact->first_name,
                'middle_name' => $contact->middle_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'facebook' => $contact->facebook,
                'skype' => $contact->skype,
                'dob' => $dob->format('m-d-Y'),
                'linkedIn' => $contact->linkedIn,
                'photo' => $contact->photo,
                'phone_number' => $contact->phone_number,
                'twitter' => $contact->twitter,
                'organization_id' => $contact->organization_id,
                'creator_id' => $contact->organization->user->id
            ]);
        }
        return $response;
    }

    public function transformLocation($location) {
        if(!empty($location)) {
            return [
                'id' => $location['id'],
                'county' => Countries::where('id',$location['state_id'])->value('name'),
                'city' => $location['name']
        ];
        }
        else return null;
    }

    public function transformComments($organization) {
        $comments = $organization->user->commentReceiver;
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

    }

    public function transformCategories($categories) {
        $response= collect();
        foreach ($categories as $category) {
            $response->push([
               'value' => $category->value,
               'description' => $category->description
            ]);
        }
        return $response;
    }

    public function transformAssets($assets) {
        $response = collect();
        foreach ($assets as $asset) {
            $response->push([
                'uuid' => $asset['uuid'],
                'url' => url('app/' . $asset['path']),
                'asset_name' => $asset['asset_name']
            ]);
        }
        return $response;
    }

    public function transformEvents($events) {
        $response = collect();
        foreach ($events as $event) {
            $response->push([
                'uuid' => $event->uuid,
                'title' => $event->title,
                'description' => $event->description,
                'country' => isset($event->volunteeringLocation['location']) ? $event->volunteeringLocation['location']['country']['name'] : null,
                'city' =>  isset($event->volunteeringLocation['location']) ? $event->volunteeringLocation['location']['name'] : null,
                'address' => $event->address,
                'start_date' => isset($event->start_date) ? $event->start_date : null,
                'end_date' => isset($event->end_date) ? $event->end_date : null,
                'category' => isset($event->category) ? $event->category->description : null
            ]);
        }
        return $response;
    }

}
