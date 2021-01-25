<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use UuidScopeTrait;

    protected $table = 'contacts';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'photo',
        'phone_number',
        'email',
        'facebook',
        'twitter',
        'linkedIn',
        'skype',
        'dob',
        'organization_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    //Organization
    public function organization() {
        return $this->belongsTo('App\Organization', 'organization_id');
    }

    public function volunteeringEvents() {
        return $this->belongsToMany('App\VolunteeringEvents','event_contact','contact_id','event_id')
            ->using('App\EventContact')
            ->withTimestamps();
    }
}
