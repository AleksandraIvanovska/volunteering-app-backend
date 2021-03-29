<?php

namespace App;

use App\Support\Extensions\CustomModel;
use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Volunteer extends Model
{
    use UuidScopeTrait, SoftDeletes;

    protected $table='volunteers';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'uuid',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'photo',
        'gender',
        'gender_id',
        'nationality_id',
        'dob',
        'cv',
        'facebook',
        'twitter',
        'linkedIn',
        'skype',
        'phone_number',
        'my_causes',
        'location_id',
        'skills',
        'instagram'
    ];

    protected $dates = [
      'created_at',
      'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Cities', 'location_id');
    }

    public function nationality() {
        return $this->belongsTo('App\Countries','nationality_id');
    }

    public function asset() {
        return $this->belongsTo('App\Asset','cv');
    }

    public function educations() {
        return $this->hasMany('App\VolunteerEducation','volunteer_id');
    }

    public function experiences() {
        return $this->hasMany('App\VolunteerExperience','volunteer_id');
    }


    public function languages()
    {
        return $this->belongsToMany('App\Language','volunteer_languages','volunteer_id','language_id')
            ->using('App\VolunteerLanguage')
            ->withPivot('level_id','uuid')
            ->withTimestamps();
    }


    public function genderType() {
        return $this->belongsTo('App\Resources','gender_id');
    }

    public function favoriteEvents() {
        return $this->belongsToMany('App\VolunteeringEvents','volunteer_favorite_events','volunteer_id','event_id')
            ->using('App\VolunteerFavoriteEvents')
            ->withTimestamps();
    }

    public function favoriteOrganizations() {
        return $this->belongsToMany('App\Organization','volunteer_favorite_organizations','volunteer_id','organization_id')
            ->using('App\VolunteerFavoriteOrganizations')
            ->withTimestamps();
    }

    public function eventAttendance() {
        return $this->belongsToMany('App\VolunteeringEvents','volunteer_event_attendance','volunteer_id','event_id')
            ->using('App\VolunteerEventAttendance')
            ->withTimestamps();
    }

    public function eventInvitations() {
        return $this->belongsToMany('App\VolunteeringEvents','volunteer_event_invitations','volunteer_id','event_id')
            ->using('App\VolunteerEventInvitations')
            ->withPivot('status_id','status')
            ->withTimestamps();
    }

}
