<?php


namespace App\Http\Controllers\Organizations\Transformers;

use App\Countries;
use App\Organization;
use App\Roles;
use Dingo\Api\Auth\Auth;
use League\Fractal\TransformerAbstract;

class OrganizationsTransformer extends TransformerAbstract
{
    public function transform(Organization $organization) {
       // return $organization;
        //ISSET ON EVERYTHING !!!!!
        return [
            'uuid' => $organization->uuid,
            'user_id' => $organization->user_id,
            'email' => $organization->user['email'],
            'role' => $organization->user->role['name'],
            'name' => $organization->name,
            'mission' => $organization->mission,
            'description' => $organization->description,
            'location' => $this->transformLocation($organization->location),
            'website' => $organization->website,
            'facebook'=> $organization->facebook,
            'linkedIn' => $organization->linkedIn,
            'phone_number' => $organization->phone_number,
            'comments' => $this->transformComments($organization),
            'categories' => $this->transformCategories($organization->categories),
            'assets' =>  $this->transformAssets($organization->assets),
            'volunteeringEvents' => $this->transformEvents($organization->volunteeringEvents)
        ];
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
        $comments=$organization->user->commentReceiver;
        $response= collect();
        foreach ($comments as $comment) {
            $response->push([
                'comment_id' => $comment->id,
                'title' => (($comment->creator->name) ? $comment->creator->name : 'Unknown user') . '<strong>  left a comment</strong> ',
                'body' => $comment->description,
                'created_date' => $comment->created_at
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
                'country' => $event->volunteeringLocation['location']['country']['name'],
                'city' => $event->volunteeringLocation['location']['name'],
                'address' => $event->address,
                'start_date' => isset($event->start_date) ? $event->start_date : null,
                'end_date' => isset($event->end_date) ? $event->end_date : null,
            ]);
        }
        return $response;
    }

}
