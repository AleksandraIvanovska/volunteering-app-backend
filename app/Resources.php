<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $table='resources';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'value',
        'description',
        'type',
        'order'
    ];

    protected $dates = [
      'created_at',
      'updated_at'
    ];

    public function eventDuration() {
        return $this->hasMany('App\VolunteeringEvents','duration_id');
    }

    public function eventExpiration() {
        return $this->hasMany('App\VolunteeringEvents','expired_id');
    }

    public function eventStatus() {
        return $this->hasMany('App\VolunteeringEvents','status_id');
    }

    public function eventGreatFor() {
        return $this->hasMany('App\VolunteeringEvents','great_for_id');
    }

    public function eventGroupSize() {
        return $this->hasMany('App\VolunteeringEvents','group_size_id');
    }

    public function volunteerGender() {
        return $this->hasMany('App\Volunteer','gender_id');
    }

    public function eventInvitedStatus() {
        return $this->hasMany('App\VolunteerEventInvitations', 'status_id');
    }
}
