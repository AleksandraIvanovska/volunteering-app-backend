<?php


namespace App\Http\Controllers\Contacts;


use App\Contact;
use App\Organization;

class ContactsService
{

    /**
     * ContactsService constructor.
     */
    public function __construct(Contact $model)
    {
        $this->model = $model;
    }

    public function create($request) {
        $organization_id = Organization::byUuid($request['organization_uuid'])->value('id');
        $contact = $this->model->create([
            'organization_id' => $organization_id,
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'name' => $request['first_name'] . " " . $request['middle_name'] . " " . $request['last_name'],
            'photo' => $request['photo'],
            'phone_number' => $request['phone_number'],
            'email' => $request['email'],
            'facebook' => $request['facebook'],
            'twitter' => $request['twitter'],
            'linkedIn' => $request['linkedIn'],
            'skype' => $request['skype'],
            'dob' => $request['dob']
        ]);

        return $contact;
    }

    public function update($request) {
        $contact = $this->model->byUuid($request['uuid'])->first();

        if (isset($request['first_name'])) {
            $name=$request['first_name'] . " " . $contact['middle_name'] . " " . $contact['last_name'];
            $contact->update(['first_name' => $request['first_name'], 'name' => $name]);
        }

        if (isset($request['middle_name'])) {
            $name=$contact['first_name'] . " " . $request['middle_name'] . " " . $contact['last_name'];
            $contact->update(['middle_name' => $request['middle_name'], 'name' => $name]);
        }

        if (isset($request['last_name'])) {
            $name=$contact['first_name'] . " " . $contact['middle_name'] . " " . $request['last_name'];
            $contact->update(['last_name' => $request['last_name'], 'name' => $name]);
        }

        if (isset($request['photo'])) {
            $contact->update(['photo' => $request['photo']]);
        }

        if (isset($request['phone_number'])) {
            $contact->update(['phone_number' => $request['phone_number']]);
        }

        if (isset($request['email'])) {
            $contact->update(['email' => $request['email']]);
        }

        if (isset($request['facebook'])) {
            $contact->update(['facebook' => $request['facebook']]);
        }

        if (isset($request['twitter'])) {
            $contact->update(['twitter' => $request['twitter']]);
        }

        if (isset($request['linkedIn'])) {
            $contact->update(['linkedIn' => $request['linkedIn']]);
        }

        if (isset($request['skype'])) {
            $contact->update(['skype' => $request['skype']]);
        }

        if (isset($request['dob'])) {
            $contact->update(['dob' => $request['dob']]);
        }
        return $contact;
    }

    public function destroy($request) {
        $contact = $this->model->byUuid($request['uuid'])->first();
        $contact->delete();
        return response()->noContent();
    }

}
