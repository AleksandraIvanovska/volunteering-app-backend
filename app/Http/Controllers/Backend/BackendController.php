<?php

namespace App\Http\Controllers\Backend;

use App\Category;
use App\Cities;
use App\Countries;
use App\Language;
use App\LanguageLevel;
use App\Organization;
use App\Resources;
use App\Roles;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;

class BackendController extends Controller
{

    public function populateRolesTable() {
        $data = array(
            array(
                'uuid' => (string)Uuid::generate(),
                'value' => 'administrator',
                'name' => 'Administrator'
            ),
            array(
                'uuid' => (string)Uuid::generate(),
                'value' => 'volunteer',
                'name' => 'Volunteer'
            ),
            array(
                'uuid' => (string)Uuid::generate(),
                'value' => 'organization',
                'name' => 'Organization'
            ),
            array(
                'uuid' => (string)Uuid::generate(),
                'value' => 'guest',
                'name' => 'Guest'
            )
        );

        foreach ($data as $role) {
            Roles::create($role);
        }
        print_r("FINISHED");
    }

    public function countriesDates() {
        $countries=Countries::all();
        foreach ($countries as $country) {
            $country['created_at']=Carbon::now()->toDateTimeString();
            $country['updated_at']=Carbon::now()->toDateTimeString();
            $country->save();
        }
        print_r("DONE");
    }

    public function citiesDates() {
        $cities=Cities::all();
        foreach ($cities as $city) {
            $city['created_at']=Carbon::now()->toDateTimeString();
            $city['updated_at']=Carbon::now()->toDateTimeString();
            $city->save();
        }
        print_r("DONE");
    }

    public function populateLanguageLevelTable() {
        $data=array(
            array(
                'value' => 'beginner',
                'description' => 'Beginner/A0-A1',
                'european_framework' => 'A0-A1'
            ),
            array(
                'value' => 'elementary',
                'description' => 'Elementary/A1',
                'european_framework' => 'A1'
            ),
            array(
                'value' => 'pre-intermediate',
                'description' => 'Pre-intermediate/A2',
                'european_framework' => 'A2'
            ),
            array(
                'value' => 'intermediate',
                'description' => 'Intermediate/B1',
                'european_framework' => 'B1'
            ),
            array(
                'value' => 'upper-intermediate',
                'description' => 'Upper-intermediate/B2',
                'european_framework' => 'B2'
            ),
            array(
                'value' => 'advanced',
                'description' => 'Advanced/C1',
                'european_framework' => 'C1'
            ),
            array(
                'value' => 'proficient',
                'description' => 'Proficient/C2',
                'european_framework' => 'C2'
            )
        );

        foreach ($data as $item) {
            LanguageLevel::create($item);
        }

        print_r("DONE");
    }

    public function populateLanguages() {
        $path = storage_path() . '/json/lang.json';
        $json=json_decode(file_get_contents($path),true);
        //$json=file_get_contents($path);
        print_r(count($json));
        //print_r($json);
        foreach ($json as $item) {
            //print_r($item['language']);
            Language::create([
                'language' => $item['language']
            ]);
        }
        print_r("DONE");
    }

    public function populateCategories() {
        $path=storage_path() . '/json/categories.json';
        $json=json_decode(file_get_contents($path),true);
        //print_r(count($json));
        foreach ($json as $item) {
            Category::create([
                'uuid' => (string)Uuid::generate(),
                'value' => $item['value'],
                'description' => $item['description']
            ]);
        }
        print_r("DONE");
    }

    public function addNationality() {
        $path=storage_path() . '/json/countries.json';
        $json=json_decode(file_get_contents($path),true);
        //print_r($json);
        $countries=Countries::all();
        foreach ($json as $item) {
            foreach ($countries as $country) {
                if ($item['id']==$country['id']){
                    //print_r($item['demonym']);
                    $country->nationality = $item['demonym'];
                    $country->save();
                }
            }
        }
        print_r("DONE");
    }

    public function populateResourcesGender() {
        $data=array(
            array(
              'value' => 'male',
              'description' => 'Male',
              'type' => 'gender_type',
              'order' => '0'
            ),
            array(
                'value' => 'female',
                'description' => 'Female',
                'type' => 'gender_type',
                'order' => '1'
            )
        );

        foreach ($data as $gender) {
            Resources::create($gender);
        }
    }

    public function populateResourcesDuration() {
        $data=array(
            array(
                'value' => '1_day',
                'description' => '1 Day',
                'type' => 'duration_type',
                'order' => '0'
            ),
            array(
                'value' => '1_week',
                'description' => '1 Week',
                'type' => 'duration_type',
                'order' => '1'
            ),
            array(
                'value' => '1_month',
                'description' => '1 Month',
                'type' => 'duration_type',
                'order' => '2'
            ),
            array(
                'value' => '2-6_months',
                'description' => '2-6 Months',
                'type' => 'duration_type',
                'order' => '3'
            ),
            array(
                'value' => '6-12_months',
                'description' => '6-12 Months',
                'type' => 'duration_type',
                'order' => '4'
            ),
            array(
                'value' => 'more_than_a_year',
                'description' => 'More than a year',
                'type' => 'duration_type',
                'order' => '5'
            )
        );

        foreach ($data as $duration) {
            Resources::create($duration);
        }
        print_r("DONE");
    }

    public function populateResourcesExpiration() {
        $data=array(
            array(
                'value' => 'active',
                'description' => 'Active',
                'type' => 'expired_type',
                'order' => '0'
            ),
            array(
                'value' => 'finished',
                'description' => 'Finished',
                'type' => 'expired_type',
                'order' => '1'
            )
        );
        foreach ($data as $item) {
            Resources::create($item);
        }
    }

    public function populateResourcesGreatFor() {
        $data=array(
            array(
                'value' => 'kids',
                'description' => 'Kids',
                'type' => 'great_for_type',
                'order' => '0'
            ),
            array(
                'value' => 'teens',
                'description' => 'Teens',
                'type' => 'great_for_type',
                'order' => '1'
            ),
            array(
                'value' => 'students',
                'description' => 'Students',
                'type' => 'great_for_type',
                'order' => '2'
            ),
            array(
                'value' => 'adults',
                'description' => 'Adults',
                'type' => 'great_for_type',
                'order' => '3'
            ),
            array(
                'value' => '55+',
                'description' => '55+',
                'type' => 'great_for_type',
                'order' => '4'
            )
        );
        foreach ($data as $item) {
            Resources::create($item);
        }
        print_r("OK");
    }

    public function populateResourcesGroupSize() {
        $data=array(
            array(
                'value' => 'up_to_5',
                'description' => 'Up to 5',
                'type' => 'group_size_type',
                'order' => '0'
            ),
            array(
                'value' => 'up_to_10',
                'description' => 'Up to 10',
                'type' => 'group_size_type',
                'order' => '1'
            ),
            array(
                'value' => 'up_to_30',
                'description' => 'Up to 30',
                'type' => 'group_size_type',
                'order' => '2'
            ),
            array(
                'value' => 'more_than_30',
                'description' => 'More than 30',
                'type' => 'group_size_type',
                'order' => '3'
            )
        );

        foreach ($data as $item) {
            Resources::create($item);
        }
    }

    public function populateResourcesEventStatuses() {
        $data=array(
            array(
                'value' => 'new',
                'description' => 'New',
                'type' => 'event_status_type',
                'order' => '0'
            ),
            array(
                'value' => 'accepting_applications',
                'description' => 'Accepting Applications',
                'type' => 'event_status_type',
                'order' => '1'
            ),
            array(
                'value' => 'ready_to_start',
                'description' => 'Ready to start',
                'type' => 'event_status_type',
                'order' => '2'
            ),
            array(
                'value' => 'happening_now',
                'description' => 'Happening now',
                'type' => 'event_status_type',
                'order' => '3'
            ),
            array(
                'value' => 'completed',
                'description' => 'Completed',
                'type' => 'event_status_type',
                'order' => '4'
            ),
            array(
                'value' => 'canceled',
                'description' => 'Canceled',
                'type' => 'event_status_type',
                'order' => '5'
            ),
            array(
                'value' => 'on_hold',
                'description' => 'On hold',
                'type' => 'event_status_type',
                'order' => '6'
            )
        );
        foreach ($data as $item) {
            Resources::create($item);
        }
    }

    public function populateVolunteerStatusesResources() {
        $data=array(
            array(
                'value' => 'invitation_sent',
                'description' => 'Invitation sent',
                'type' => 'event_volunteer_status_type',
                'order' => '0'
            ),
            array(
                'value' => 'invitation_approved',
                'description' => 'Invitation approved',
                'type' => 'event_volunteer_status_type',
                'order' => '1'
            ),
            array(
                'value' => 'invitation_rejected',
                'description' => 'Invitation rejected',
                'type' => 'event_volunteer_status_type',
                'order' => '2'
            ),
            array(
                'value' => 'invitation_canceled',
                'description' => 'Invitation canceled',
                'type' => 'event_volunteer_status_type',
                'order' => '3'
            ),
            array(
                'value' => 'not_responding',
                'description' => 'Not responding',
                'type' => 'event_volunteer_status_type',
                'order' => '4'
            ),
            array(
                'value' => 'request_sent',
                'description' => 'Request sent',
                'type' => 'event_volunteer_status_type',
                'order' => '5'
            ),
            array(
                'value' => 'request_approved',
                'description' => 'Request approved',
                'type' => 'event_volunteer_status_type',
                'order' => '6'
            ),
            array(
                'value' => 'request_rejected',
                'description' => 'Request rejected',
                'type' => 'event_volunteer_status_type',
                'order' => '7'
            ),
            array(
                'value' => 'request_withdrawn',
                'description' => 'Request withdrawn',
                'type' => 'event_volunteer_status_type',
                'order' => '8'
            ),
            array(
                'value' => 'attended',
                'description' => 'Attended',
                'type' => 'event_volunteer_status_type',
                'order' => '9'
            ),
            array(
                'value' => 'did_not_attend',
                'description' => 'Did not attend',
                'type' => 'event_volunteer_status_type',
                'order' => '10'
            ),
            array(
                'value' => 'none',
                'description' => 'None',
                'type' => 'event_volunteer_status_type',
                'order' => '11'
            )
        );

        foreach ($data as $item) {
            Resources::create($item);
        }
    }


    public function index() {
        //return $this->populateRolesTable();
        //return $this->countriesDates();
        //return $this->citiesDates();
        //return $this->populateLanguageLevelTable();
        //return $this->populateLanguages();
        //return $this->populateCategories();
        //return $this->addNationality();
//        Organization::create([
//            'uuid' => (string)Uuid::generate(),
//            'name' =>'test',
//            'user_id' => 4
//        ]);
//        $data=User::with('organization')->get();
//        print_r(json_encode($data));

        //return $this->populateResourcesGender();
        //return $this->populateResourcesDuration();
        //return $this->populateResourcesExpiration();
        //return $this->populateResourcesGreatFor();
        //return $this->populateResourcesGroupSize();
        //return $this->populateResourcesEventStatuses();
        //return $this->populateVolunteerStatusesResources();

//        $user=User::where('id',4)->with('organization')->get();
//        print_r(json_encode($user));

        //return Organization::where('uuid',"9dbf5ba0-9155-11ea-8b71-513f58d2ba9c")->with('location.country')->first();
        return Organization::where('uuid','9dbf5ba0-9155-11ea-8b71-513f58d2ba9c')->with('contacts')->first();

    }
}
