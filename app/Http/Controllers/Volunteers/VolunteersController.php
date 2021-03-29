<?php

namespace App\Http\Controllers\Volunteers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VolunteersController extends Controller
{
    /**
     * VolunteersController constructor.
     */
    public function __construct(VolunteersService $volunteersService)
    {
        $this->volunteersService = $volunteersService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->volunteersService->getAll($request);
    }

    public function getByUuid(Request $request, $uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator = Validator::make($request->all(),[
           'uuid' => 'required|exists:volunteers,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->getByUuid($uuid);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id|unique:volunteers,user_id',
            'first_name' => 'required|string',
//            'middle_name' => 'present|nullable|string',
//            'last_name' =>'present|nullable|string',
//            //'gender_id' => 'present|nullable|exists:resources,id,type,gender_type',
//            'gender' => 'present|nullable|string|exists:resources,value,type,gender_type',
//            'photo' => 'present|nullable|string',
//            //'nationality_id' => 'present|nullable|exists:countries,id',
//            'nationality' => 'present|nullable|exists:countries,nationality',
//            //'location_id' => 'present|nullable|exists:cities,id',
//            'city' => 'present|nullable|exists:cities,name|string',
//            'phone_number' => 'present|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/',
//            'dob' => 'present|nullable',
//            'cv' => 'present|nullable|exists:assets,id',
//            'facebook' => 'present|nullable|string|url',
//            'linkedIn' => 'present|nullable|string|url',
//            'twitter' => 'present|nullable|string|url',
//            'skype' => 'present|nullable|string',
//            'my_causes' => 'present|nullable|array',
//           // 'my_causes.*.value' => 'required_with:my_causes|string',
//            'skills' => 'present|nullable|array',
//          //  'skills.*.value' => 'required_with:skills|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        //return $request;
        return $this->volunteersService->create($request);
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
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|exists:volunteers,uuid',
            'first_name' => 'filled|string',
            'middle_name' => 'sometimes|nullable|string',
            'last_name' => 'sometimes|nullable|string',
            'gender' => 'sometimes|nullable|string|exists:resources,value,type,gender_type',
            'photo' => 'sometimes|nullable|string',
            'nationality' => 'sometimes|nullable|exists:countries,nationality',
            'dob' => 'sometimes|nullable|date',
            'cv' => 'sometimes|nullable|exists:assets,id',
            'facebook' => 'sometimes|nullable|string|url',
            'linkedIn' => 'sometimes|nullable|string|url',
            'twitter' => 'sometimes|nullable|string|url',
            'skype' => 'sometimes|nullable|string',
            'phone_number' => 'sometimes|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/',
            'my_causes' => 'sometimes|nullable|array',
            'skills' => 'sometimes|nullable|array',
            'city' => 'sometimes|nullable|exists:cities,name'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->update($request);
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
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteers,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->destroy($request);
    }

    public function createVolunteerLanguage(Request $request) {
        $validator=Validator::make($request->all(),[
            'volunteer_uuid' => 'required|exists:volunteers,uuid',
            'language' => 'required|exists:languages,language',
           // 'level' => 'present',
            'level' => 'required|exists:language_level,value',
           // 'level.description' => 'required_with:level|exists:language_level,description'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->createVolunteerLanguage($request);
    }

    public function updateVolunteerLanguage(Request $request,$uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'sometimes|exists:volunteer_languages,uuid',
            'language' => 'filled|exists:languages,language',
            //'level' => 'sometimes',
            'level.value' => 'sometimes|exists:language_level,value',
            //'level.description' => 'required_with:level|exists:language_level,description'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->updateVolunteerLanguage($request);
    }

    public function deleteVolunteerLanguage(Request $request, $uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_languages,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->deleteVolunteerLanguage($request);
    }

    public function createFavoriteOrganization(Request $request) {
        $validator=Validator::make($request->all(),[
            'volunteer_uuid' => 'required|exists:volunteers,uuid',
            'organization_name' => 'required|exists:organizations,name'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->createFavoriteOrganization($request);
    }

    public function updateFavoriteOrganization(Request $request,$uuid) {
        //
    }

    public function deleteFavoriteOrganization(Request $request, $uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
           'uuid' => 'required|exists:volunteer_favorite_organizations,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->deleteFavoriteOrganization($request);
    }

    public function createFavoriteEvent(Request $request) {
        $validator=Validator::make($request->all(),[
            'volunteer_uuid' => 'required|exists:volunteers,uuid',
            'event_name' => 'required|exists:volunteering_events,title'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->createFavoriteEvent($request);
    }

    public function deleteFavoriteEvent(Request $request, $uuid) {
        $request->merge(['uuid' => $uuid]);
        $validator=Validator::make($request->all(),[
            'uuid' => 'required|exists:volunteer_favorite_events,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->deleteFavoriteEvent($request);
    }


    public function createComment(Request $request) {

        $validator = Validator::make($request->all(),[
           'volunteer_uuid' => 'required|exists:volunteers,uuid',
           'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->createComment($request);
    }

    public function updateComment(Request $request, $comment_uuid) {
        $request->merge(['comment_uuid' => $comment_uuid]);
        $validator = Validator::make($request->all(),[
            'comment_uuid' => 'required|exists:comments,uuid',
            'description' => 'filled|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->updateComment($request);
    }

    public function deleteComment(Request $request, $comment_uuid) {
        $request->merge(['comment_uuid' => $comment_uuid]);
        $validator = Validator::make($request->all(),[
            'comment_uuid' => 'required|exists:comments,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->volunteersService->deleteComment($request);
    }
}
