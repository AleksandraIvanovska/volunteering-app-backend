<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $table='cities';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable=[
        'name',
        'state_id',
        'updated_at',
        'created_at'
    ];

    protected $dates=[
      'created_at',
      'updated_at'
    ];

    public function country() {
        return $this->belongsTo('App\Countries', 'state_id');
    }

    public function organization() {
        return $this->hasMany('App\Organization','location_id');
    }

    public function volunteer() {
        return $this->hasMany('App\Volunteer', 'location_id');
    }

    public function volunteerExperience() {
        return $this->hasMany('App\VolunteerExperience','location_id');
    }

//    public function volunteeringEvent() {
//        return $this->hasMany('App\VolunteeringEvents','location_id');
//    }

    public function eventLocation() {
        return $this->hasMany('App\EventLocation','location_id');
    }
}
