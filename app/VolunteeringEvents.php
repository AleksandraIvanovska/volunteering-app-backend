<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteeringEvents extends Model
{
    use SoftDeletes, UuidScopeTrait;

    protected $table='volunteering_events';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable = [
      'id',
      'uuid',
      'title',
      'description',
      'organization_id',
      'category_id',
      'is_virtual',
      'location_id',
      'address',
      'ongoing',
      'start_date',
      'end_date',
      'estimated_hours',
      'average_hours_per_day',
      'duration_id',
      'deadline',
      'expired_id',
      'status_id',
      'volunteers_needed',
      'spaces_available',
      'great_for_id',
      'group_size_id',
      'sleeping',
      'food',
      'transport',
      'benefits',
      'skills_needed',
      'tags',
      'notes',
        'virtual_info'

    ];

    protected $dates=[
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function organization() {
        return $this->belongsTo('App\Organization','organization_id');
    }

    public function category() {
        return $this->belongsTo('App\Category','category_id');
    }

//    public function location() {
//        return $this->belongsTo('App\Cities','location_id');
//    }

    public function duration() {
        return $this->belongsTo('App\Resources','duration_id');
    }

    public function expiration() {
        return $this->belongsTo('App\Resources','expired_id');
    }

    public function status() {
        return $this->belongsTo('App\Resources','status_id');
    }

    public function greatFor() {
        return $this->belongsTo('App\Resources','great_for_id');
    }

    public function groupSize() {
        return $this->belongsTo('App\Resources','group_size_id');
    }

    public function volunteeringLocation() {
        return $this->hasOne('App\EventLocation','event_id');
    }

    public function assets() {
        return $this->belongsToMany('App\Asset','event_asset','event_id','asset_id')
            ->using('App\EventAsset')
            ->withTimestamps();
    }

    public function contacts() {
        return $this->belongsToMany('App\Contact','event_contact','event_id','contact_id')
            ->using('App\EventContact')
            ->withTimestamps();
    }

    public function requirements() {
        return $this->hasOne('App\EventRequirements','event_id');
    }

    public function volunteerFavorites() {
        return $this->belongsToMany('App\Volunteer','volunteer_favorite_events','event_id','volunteer_id')
            ->using('App\VolunteerFavoriteEvents')
            ->withTimestamps();
    }

    public function volunteerAttendance() {
        return $this->belongsToMany('App\Volunteer','volunteer_event_attendance','event_id','volunteer_id')
            ->using('App\VolunteerEventAttendance')
            ->withTimestamps();
    }

    public function volunteerInvitations() {
        return $this->belongsToMany('App\Volunteer','volunteer_event_invitations','event_id','volunteer_id')
            ->using('App\VolunteerEventInvitations')
            ->withPivot('status_id','status','uuid')
            ->withTimestamps();
    }
}
