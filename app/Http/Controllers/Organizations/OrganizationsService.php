<?php


namespace App\Http\Controllers\Organizations;

use App\Asset;
use App\Category;
use App\Cities;
use App\Comments;
use App\Contact;
use App\Http\Controllers\Organizations\Transformers\OrganizationsTransformer;
use App\Organization;
use App\OrganizationAsset;
use App\OrganizationCategories;
use App\Support\HasRoleTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function App\Helpers\transform_organization_asset;

use App\Jobs\Notifications\CommentForOrganizationWasMade as NotificationComment;


class OrganizationsService
{

    protected $model, $transformer;
    use HasRoleTrait;

    /**
     * OrganizationsService constructor.
     */
    public function __construct(Organization $model, OrganizationsTransformer $transformer)
    {
        $this->model = $model;
        $this->transformer = $transformer;
    }

    public function getAll(Request $request) {
        $organizations = $this->model->query();

        if ($request->has('search')) {
            $organizations->where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('description', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('country')) {
            $organizations->whereHas('location.country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('country') . '%');
            });
        }

        if ($request->has('city')) {
            $organizations->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('city') . '%');
            });
        }

        if ($request->has('category')) {
            $organizations->whereHas('categories', function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->input('category') . '%');
            });
        }

        $organizations = $organizations->get();
        return $organizations->map(function ($item) {
            return $this->getByUuid($item->uuid);
        });
        return $organizations;
    }

    public function getByUuid($uuid) {
            $organization = $this->model->byUuid($uuid)->with([
                'user' => function($query) {
                    $query->select('id','email','role_id');
                },
                'user.commentReceiver',
                'location.country' => function ($query) {
                    $query->select('id','name','countries.id','countries.name');
                },
                'categories' => function ($query) {
                    $query->select('value','description');
                },
                'contacts',
                'assets',
                'volunteeringEvents',

            ])->firstOrFail();

            //return $organization;
            return $this->transformer->transform($organization);

    }


    public function create(Request $request) {
        $organization=$request->all();
        $organizationData=Organization::create([
            'user_id' => $organization['user_id'],
            'name' => isset($organization['name']) ? $organization['name'] : null,
            'mission' => isset($organization['mission']) ? $organization['mission'] : null,
            'description' => isset($organization['description']) ? $organization['description'] : null,
            'location_id' => isset($organization['city']) ? Cities::where('name',$organization['city'])->value('id') : null,
            'website' => isset($organization['website']) ? $organization['website'] : null,
            'facebook'=> isset($organization['facebook']) ? $organization['facebook'] : null,
            'linkedIn' => isset($organization['linkedIn']) ? $organization['linkedIn'] : null,
            'phone_number' => isset($organization['phone_number']) ? $organization['phone_number'] : null,
            'photo' => isset($organization['photo']) ? $organization['photo'] : null
        ]);
        User::where('id',$organizationData['user_id'])->update(['name'=>$organizationData['name']]);

        $response = [];
        $response['data'] = $this->transformer->transform($organizationData);
        return $response;
    }


    public function update(Request $request) {
        $data=$request->all();
        $organization=$this->model::byUuid($data['uuid'])->first();
        if (array_key_exists('name',$data)) {
            $organization->update(['name' => $data['name']]);
            $user=User::where('id', $organization['user_id'])->first();
            $user->update(['name' => $data['name']]);
        }

        if (array_key_exists('mission',$data)) {
            $organization->update(['mission' => $data['mission']]);
        }

        if (array_key_exists('description',$data)) {
            $organization->update(['description' => $data['description']]);
        }

        if (array_key_exists('photo', $data)) {
            $organization->update(['photo' => $data['photo']]);
            $organization->save();
        }

        if (array_key_exists('city', $data)) {
            $city_id = Cities::where('name',$data['city'])->value('id');
            $organization->update(['location_id' => $city_id]);
        }

        if (array_key_exists('website', $data)) {
            $organization->update(['website' => $data['website']]);
        }

        if (array_key_exists('facebook', $data)) {
            $organization->update(['facebook' => $data['facebook']]);
        }

        if (array_key_exists('linkedIn',$data)) {
            $organization->update(['linkedIn' => $data['linkedIn']]);
        }

        if (array_key_exists('phone_number', $data)) {
            $organization->update(['phone_number' => $data['phone_number']]);
        }

        if (!empty($data['category'])) {
            $organization->categories()->detach();
            foreach ($data['category'] as $category) {
                $category_id = Category::where('value', $category)->value('id');
                if ($category_id) {
                    $organization->categories()->attach($category_id);
                } else {
                    $new_category = Category::create([
                        'value' => $category,
                        'description' => $category
                    ]);
                    $organization->categories()->attach($new_category->id);
                }
            }
        }

        return [
            "message" => "Organization has been successfully updated"
        ];

        $response=[];
        $response['data'] = $this->transformer->transform($organization);
        return $response;

    }

    public function destroy($uuid) {
        $organization=$this->model::byUuid($uuid)->first();

        if (!empty($organization->categories)) {
            $organization->categories()->detach();
        }

        if (!empty($organization->assets)) {
            $organization->assets()->detach();
        }

        $organization->delete();

        return response()->noContent();
    }

    public function createOrganizationAsset(Request $request) {
        $data=$request->all();

        $asset=Asset::where('uuid',$data['asset_uuid'])->first();
        $organization_id=Organization::where('uuid',$data['organization_uuid'])->value('id');

        $organization_asset = OrganizationAsset::create([
            'asset_id' => $asset['id'],
            'organization_id' => $organization_id
        ]);

        return transform_organization_asset($organization_asset,$asset);
    }

    public function deleteOrganizationAsset($request) {
        $organization_asset=OrganizationAsset::byUuid($request['organization_asset_uuid'])->first();
        $asset=Asset::find($organization_asset['asset_id']);
        $asset->delete();
        Storage::delete($asset->path);
        $organization_asset->delete();

        return response()->noContent();
    }

    public function createComment($request) {
        $comment = "";
        $organization_id = Organization::where('uuid' , $request['organization_uuid'])->value('user_id');
      //  if ($this->isVolunteer(Auth::user())) {
            $comment = Comments::create([
                'description' => $request['description'],
                'user_id' => $organization_id,
                'creator_id' => Auth::user()->id
            ]);
       // }

        NotificationComment::dispatch(Auth::user(), $organization_id);

        //Send email and notification
        $createdAt = Carbon::parse($comment['created_at']);
        return [
            'comment_id' => $comment->id,
            'comment_uuid' => $comment->uuid,
            'body' => $comment->description,
            'created_date' => $createdAt->format('M d Y'),
            'creator' => ($comment->creator) ? $comment->creator->name : null,
            'creator_id' => Auth::user()->id
        ];
    }

    public function updateComment($request) {

        $comment = Comments::where('uuid', $request['comment_uuid'])->first();

        if ($comment->creator_id == Auth::user()->volunteer['id']) {
            if (isset($request['description'])) {
                $comment->update(['description' => $request['description']]);
            }
        }
        return $comment;
    }

    public function deleteComment($request) {
        $comment = Comments::where('uuid', $request['comment_uuid'])->first();

      //  if ($comment->creator_id == Auth::user()->volunteer['id']) {
            $comment->delete();
      //  }

        return response(['message' => 'Comment successfully deleted']);

    }


    public function getOrganizationContacts($request) {
        return Contact::where('organization_id', Organization::where('uuid', $request['uuid'])->value('id'))->get();
    }
}
