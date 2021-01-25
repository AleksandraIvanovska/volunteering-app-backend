<?php

namespace App\Http\Controllers\Organizations;

use App\Asset;
use App\Organization;
use App\OrganizationAsset;
use App\Support\HasRoleTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Validator;



class OrganizationsController extends Controller
{
    use Helpers,HasRoleTrait;

    /**
     * OrganizationsController constructor.
     */
    public function __construct(OrganizationsService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->organizationService->getAll($request);
    }

    public function getByUuid(Request $request,$uuid) {

        $request->merge(['uuid'=>$uuid]);
        $validator = Validator::make($request->all(),[
            'uuid' => 'required|exists:organizations,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->getByUuid($uuid);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'user_id' => 'required|exists:users,id|unique:organizations,user_id',
            'mission' => 'present|string|nullable',
            'description' => 'present|string|nullable',
            'photo' => 'present|string|nullable',
            'city' => 'present|nullable|exists:cities,name|string',
            'website' => 'present|nullable|string|url',
            'facebook' => 'present|nullable|string|url',
            'linkedIn' => 'present|nullable|string|url',
            'phone_number' => 'present|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->create($request);
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
            'uuid' => 'required|exists:organizations,uuid',
            'name' => 'filled|string',
            'mission' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'photo' => 'sometimes|nullable|url',
            //'county' => 'sometimes|exists:countries,name'
            'city' => 'sometimes|nullable|exists:cities,name|string',
            'website' => 'sometimes|nullable|string|url',
            'facebook' => 'sometimes|nullable|string|url',
            'linkedIn' => 'sometimes|nullable|string|url',
            'phone_number' => 'sometimes|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->update($request);
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
           'uuid' => 'required|exists:organizations,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->destroy($uuid);
    }

    public function createOrganizationAsset(Request $request, $uuid) {
        $request->merge(['organization_uuid' => $uuid]);
        $validator = Validator::make($request->all(),[
            'organization_uuid' => 'required|exists:organizations,uuid',
            'asset_uuid' => ['required','exists:assets,uuid',
                function($attribute, $value, $fail) use($request) {
                    if (isset($request['asset_uuid']) && isset($request['organization_uuid'])) {
                        $asset_id=Asset::where('uuid',$request['asset_uuid'])->value('id');
                        $organization_id = Organization::where('uuid',$request['organization_uuid'])->value('id');
                        if (!empty($organization_id) && !empty($asset_id)) {
                            if (OrganizationAsset::where('organization_id',$organization_id)->where('asset_id',$asset_id)->exists()) {
                                return $fail('This file is already assigned to this organization');
                            }
                        }
                    }
                }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->createOrganizationAsset($request);
    }

    public function updateOrganizationAsset(Request $request, $uuid) {
        $request->merge(['organization_uuid' => $uuid]);

        $validator=Validator::make($request->all(),[
           'organization_uuid' => 'required|exists:organizations,uuid',
           'organization_asset_uuid' => 'required|exists:organization_asset,uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return;
        return $this->organizationService->updateOrganizationAsset($request);
    }

    public function deleteOrganizationAsset(Request $request, $organization_uuid,$organization_asset_uuid) {
        $request->merge(['organization_uuid' => $organization_uuid,'organization_asset_uuid' => $organization_asset_uuid]);

        $validator=Validator::make($request->all(),[
            'organization_uuid' => 'required|exists:organizations,uuid',
            'organization_asset_uuid' => ['required','exists:organization_asset,uuid',
                function($attribute, $value ,$fail) use ($request) {
                    if(array_key_exists('organization_asset_uuid',$request->all())) {
                        $organization_asset=OrganizationAsset::byUuid($request['organization_asset_uuid'])->first();
                        if(isset($organization_asset->uuid) && Organization::find($organization_asset->organization_id)->uuid != $request['organization_uuid']) {
                            return $fail("This file is not assigned to this organization");
                        }
                    }
                }]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->deleteOrganizationAsset($request);

    }



    public function createComment(Request $request) {

        $validator = Validator::make($request->all(),[
            'organization_uuid' => 'required|exists:organizations,uuid',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->createComment($request);
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

        return $this->organizationService->updateComment($request);
    }

    public function deleteComment(Request $request, $comment_uuid) {
        $request->merge(['comment_uuid' => $comment_uuid]);
        $validator = Validator::make($request->all(),[
            'comment_uuid' => 'required|exists:comments,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->organizationService->deleteComment($request);
    }

}
