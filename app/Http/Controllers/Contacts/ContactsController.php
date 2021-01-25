<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    /**
     * ContactsController constructor.
     */
    public function __construct(ContactsService $service)
    {
        $this->service = $service;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'organization_uuid' => 'required|exists:organizations,uuid',
            'first_name' => 'required|string',
            'middle_name' => 'present|string|nullable',
            'last_name' => 'present|string|nullable',
            'photo' => 'present|string|nullable',
            'phone_number' => 'present|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/',
            'email' => 'required|email',
            'facebook' => 'present|nullable|string|url',
            'twitter' => 'present|nullable|string|url',
            'linkedIn' => 'present|nullable|string|url',
            'skype' => 'present|string|nullable',
            'dob' => 'present|nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->service->create($request);
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
            'uuid' => 'required|exists:contacts,uuid',
            'first_name' => 'filled|string',
            'middle_name' => 'sometimes|string|nullable',
            'last_name' => 'sometimes|string|nullable',
            'photo' => 'sometimes|string|nullable',
            'phone_number' => 'sometimes|nullable|regex:/^[0-9\-\(\)\/\+\s]*$/',
            'email' => 'sometimes|email',
            'facebook' => 'sometimes|nullable|string|url',
            'twitter' => 'sometimes|nullable|string|url',
            'linkedIn' => 'sometimes|nullable|string|url',
            'skype' => 'sometimes|string|nullable',
            'dob' => 'sometimes|nullable|date'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->service->update($request);
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
            'uuid' => 'required|exists:contacts,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(),403);
        }

        return $this->service->destroy($request);
    }
}
