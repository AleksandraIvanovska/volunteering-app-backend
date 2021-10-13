<?php


namespace App\Http\Controllers\Volunteers;


use App\Cities;
use App\Comments;
use App\Countries;
use App\Http\Controllers\Volunteers\Transformers\VolunteersTransformer;
use App\Language;
use App\LanguageLevel;
use App\Organization;
use App\Resources;
use App\Support\HasRoleTrait;
use App\User;
use App\Volunteer;
use App\VolunteerFavoriteEvents;
use App\VolunteerFavoriteOrganizations;
use App\VolunteeringEvents;
use App\VolunteerLanguage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Jobs\Notifications\CommentForVolunteerWasMade as NotificationComment;

class VolunteersService
{

    protected $model, $transformer;
    use HasRoleTrait;

    /**
     * VolunteersService constructor.
     */
    public function __construct(Volunteer $model, VolunteersTransformer $transformer)
    {
        $this->model = $model;
        $this->transformer = $transformer;
    }

    public function getAll(Request $request) {
        $volunteers = $this->model->query();

        if ($request->has('search')) {
            $volunteers->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('country')) {
            $volunteers->whereHas('location.country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('country') . '%');
            });
        }

        if ($request->has('city')) {
            $volunteers->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('city') . '%');
            });
        }

        $volunteers = $volunteers->get();
        return $volunteers->map(function ($item) {
            return $this->getByUuid($item->uuid);
        });
        //return $volunteers;
    }

    public function getByUuid($uuid) {
        $voluneer = $this->model->byUuid($uuid)->with([
            'user' => function($query) {
                $query->select('id','email','role_id');
            },
            'user.commentReceiver' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'location' => function($query) {
                $query->select('id','name','state_id');
            },
            'location.country' => function($query) {
                $query->select('id','name');
            },
            'nationality' => function($query) {
                $query->select('id','nationality');
            },
            'asset' => function($query) {
                $query->get();
            },
            'educations' => function($query) {
                $query->select('id', 'uuid' ,'volunteer_id','institution_name','degree_name','major','start_date','graduation_date')->orderBy('start_date', 'desc');;
            },
            'experiences' => function($query) {
                $query->select('id', 'uuid' ,'volunteer_id','job_title','company_name','location_id','start_date','end_date')->orderBy('start_date', 'desc');
            },
            'experiences.location' => function($query) {
                $query->select('id','name','state_id');
            },
            'experiences.location.country' => function($query) {
                $query->select('id', 'name');
            },
            'genderType' => function($query) {
                $query->select('id','value','description');
            },
            'languages' => function($query) {
                $query->get();
            },
            //'languages.pivot.languageLevel'
            'favoriteEvents' => function($query) {
                $query->get();
            },
            'favoriteOrganizations',
            'eventAttendance',
            'eventInvitations'

//            'languages.languageLevel' => function($query) {
//                $query->select('id','value','description','european_framework');
//            }

        ])->firstOrFail();

        return $this->transformer->transform($voluneer);

    }

    public function create($request) {
        $data=$request->all();
        $volunteerData=$this->model->create([
            'user_id' => $data['user_id'],
            'first_name' => $data['first_name'],
            'middle_name' => isset($data['middle_name']) ? $data['middle_name'] : null,
            'last_name' => isset($data['last_name']) ? $data['last_name'] : null,
            'name' => $data['first_name'] . " " . ($data['middle_name'] ?? null) . " " . ($data['last_name'] ?? null),
            'photo' => $data['photo'] ?? null,
            'gender_id' => isset($data['gender']) ? Resources::where('value',$data['gender'])->value('id') : null,
            'gender' => isset($data['gender']) ? Resources::where('value',$data['gender'])->value('description') : null,
            'nationality_id' => isset($data['nationality']) ? Countries::where('nationality',$data['nationality'])->value('id') : null,
            'dob' => $data['dob'] ?? null, //Carbon ??
            'cv' => $data['cv'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'twitter' => $data['twitter'] ?? null,
            'linkedIn' => $data['linkedIn'] ?? null,
            'skype' => $data['skype'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'skills' => (isset($data['skills']) && is_array($data['skills']) && !empty($data['skills'])) ? $data['skills'] : null,
            'my_causes' => (isset($data['my_causes']) && is_array($data['my_causes']) && !empty($data['my_causes'])) ? $data['my_causes'] : null

        ]);

        User::where('id',$volunteerData['user_id'])->update(['name'=>$volunteerData['name']]);

//        $response = [];
//        $response['data'] = $this->transformer->transform($volunteerData);
//        return $response;

        return $volunteerData;
    }

    public function update($request) {
        $data=$request->all();
        $volunteer=$this->model->byUuid($data['uuid'])->first();
        $user=User::find($volunteer['user_id']);

        if (array_key_exists('first_name',$data)) {
            $name=$data['first_name'] . " " . $volunteer['middle_name'] . " " . $volunteer['last_name'];
            $volunteer->update(['first_name' => $data['first_name'],'name' => $name]);
            $user->update(['name' => $name]);
        }

        if (array_key_exists('middle_name',$data)) {
            $name=$volunteer['first_name'] . " " . $data['middle_name'] . " " . $volunteer['last_name'];
            $volunteer->update(['middle_name' => $data['middle_name'], 'name' => $name]);
            $user->update(['name' => $name]);
        }

        if (array_key_exists('last_name',$data)) {
            $name=$volunteer['first_name'] . " " . $volunteer['middle_name'] . " " . $data['last_name'];
            $volunteer->update(['last_name' => $data['last_name'], 'name' => $name]);
            $user->update(['name' => $name]);
        }

        if (array_key_exists('gender',$data)) {
            $volunteer->update(['gender' => Resources::where('value', $data['gender'])->value('description'), 'gender_id' => Resources::where('value', $data['gender'])->value('id')]);
        }

        if (array_key_exists('photo',$data)) {
            $volunteer->update(['photo' => $data['photo']]);
        }

        if (array_key_exists('dob',$data)) {
            $volunteer->update(['dob' => $data['dob']]);
        }

        if (array_key_exists('nationality',$data)) {
            $volunteer->update(['nationality_id' => Countries::where('nationality', $data['nationality'])->value('id')]);
        }

        //?????????????
//        if (isset($data['cv'])) {
//            $volunteer->update(['cv' => $data['cv']]);
//        }

        if (array_key_exists('facebook',$data)) {
            $volunteer->update(['facebook' => $data['facebook']]);
        }

        if (array_key_exists('linkedIn',$data)) {
            $volunteer->update(['linkedIn' => $data['linkedIn']]);
        }

        if (array_key_exists('twitter',$data)) {
            $volunteer->update(['twitter' => $data['twitter']]);
        }

        if (array_key_exists('skype',$data)) {
            $volunteer->update(['skype' => $data['skype']]);
        }

         if (array_key_exists('instagram',$data)) {
             $volunteer->update(['instagram' => $data['instagram']]);
         }

        if (array_key_exists('phone_number',$data)) {
            $volunteer->update(['phone_number' => $data['phone_number']]);
        }

        if (array_key_exists('skills',$data)) {
            $volunteer->update(['skills' => $data['skills']]);
        }

        if (array_key_exists('my_causes',$data)) {
            $volunteer->update(['my_causes' => $data['my_causes']]);
        }

        if (array_key_exists('city',$data)) {
            $volunteer->update(['location_id' => Cities::where('name',$data['city'])->value('id')]);
        }

        return [
            "message" => "Volunteer has been successfully updated"
        ];

        return $volunteer;
        $response=[];
        $response['data'] = $this->transformer->transform($volunteer);
        return $response;
    }

    public function destroy($request) {
        //return $request;
        $volunteer=$this->model->byUuid($request['uuid'])->first();

        //HERE ALSO DELETE THE EDUCATIONS,EXPERIENCES AND OTHER CONNECTED TABLES

        $volunteer->delete();

        return response()->noContent();
    }


    public function createVolunteerLanguage($request) {
        $volunteer_id=$this->model->byUuid($request['volunteer_uuid'])->value('id');
        //return $volunteer_id;
        $volunteer_language=VolunteerLanguage::create([
            'volunteer_id' => $volunteer_id,
            'language_id' => Language::where('language',$request['language'])->value('id'),
            'level_id' => LanguageLevel::where('value', $request['level'])->value('id')
        ]);

        return [
            "message" => "Language successfully added"
        ];

        return $volunteer_language;
    }

    public function updateVolunteerLanguage($request) {
        $volunteer_language = VolunteerLanguage::byUuid($request['uuid'])->first();
        //return $volunteer_language;

        if (isset($request['language'])) {
            $volunteer_language->update(['language_id' => Language::where('language',$request['language'])->value('id')]);
        }

        if (isset($request['level'])) {
            if ($request['level']) {
                $volunteer_language->languageLevel()->associate(LanguageLevel::where('value',$request['level'])->value('id'));
                $volunteer_language->save();
            }
            elseif ($request['level'] === null) {
                $volunteer_language->languageLevel()->dissociate();
                $volunteer_language->save();
            }
        }

        return [
            "message" => "Language successfully updated"
        ];
        return $volunteer_language;
    }

    public function deleteVolunteerLanguage($request) {
        $volunteer_language=VolunteerLanguage::byUuid($request['uuid'])->first();
        $volunteer_language->delete();

        return [
            "message" => "Language successfully delete"
        ];

        return response()->noContent();
    }

    public function createFavoriteOrganization($request) {
        $volunteer_id = $this->model->byUuid($request['volunteer_uuid'])->value('id');
        $volunteer_favorite_organization=VolunteerFavoriteOrganizations::create([
            'volunteer_id' => $volunteer_id,
            //'organization_id' => Organization::where('name', $request['organization_name'])->value('id')
            'organization_id' => Organization::where('uuid', $request['organization_uuid'])->value('id')

        ]);

        return [
            "message" => "Organization added to favorite organizations"
        ];
        return $volunteer_favorite_organization;
    }


    public function deleteFavoriteOrganization($request) {
        $volunteer_favorite_organization=VolunteerFavoriteOrganizations::byUuid($request['uuid'])->first();
        $volunteer_favorite_organization->delete();

        return [
            "message" => "Organization removed from favorite organizations"
        ];
        return response()->noContent();
    }

    public function createFavoriteEvent($request) {
        $volunteer_id = $this->model->byUuid($request['volunteer_uuid'])->value('id');
        $volunteer_favorite_event = VolunteerFavoriteEvents::create([
            'volunteer_id' => $volunteer_id,
           // 'event_id' => VolunteeringEvents::where('title',$request['event_name'])->value('id')
            'event_id' => VolunteeringEvents::where('uuid',$request['event_uuid'])->value('id')
        ]);

        return [
            "message" => "Event added to favorite events"
        ];

        return $volunteer_favorite_event;
    }

    public function deleteFavoriteEvent($request) {
        $volunteer_favorite_event=VolunteerFavoriteEvents::byUuid($request['uuid'])->first();
        $volunteer_favorite_event->delete();

        return [
            "message" => "Event removed from favorite events"
        ];
        return response()->noContent();
    }

    public function createComment($request) {
       $comment = "";
       //$volunteer_id = Volunteer::where('uuid' , $request['volunteer_uuid'])->value('id');
        $volunteer_id = Volunteer::where('uuid' , $request['volunteer_uuid'])->value('user_id');

        // if ($this->isOrganization(Auth::user())) {
            $comment = Comments::create([
                'description' => $request['description'],
                'user_id' => $volunteer_id,
                'creator_id' => Auth::user()->id
            ]);
     //   }

        //Send email and notification
        NotificationComment::dispatch(Auth::user(), $volunteer_id);

        $createdAt = Carbon::parse($comment['created_at']);
        return [
            'comment_id' => $comment->id,
            'comment_uuid' => $comment->uuid,
            'body' => $comment->description,
            'created_date' => $createdAt->format('M d Y'),
            'creator' => ($comment->creator) ? $comment->creator->name : null,
            'creator_id' => Auth::user()->id
        ];

        return $comment;
    }

    public function updateComment($request) {

        $comment = Comments::where('uuid', $request['comment_uuid'])->first();

        if ($comment->creator_id == Auth::user()->organization['id']) {
            if (isset($request['description'])) {
                $comment->update(['description' => $request['description']]);
            }
        }

        return $comment;
    }

    public function deleteComment($request) {
        $comment = Comments::where('uuid', $request['comment_uuid'])->first();

       // if ($comment->creator_id == Auth::user()->organization['id']) {
            $comment->delete();
      //  }
        return response(['message' => 'Comment successfully deleted']);

        return response()->noContent();
    }

}
