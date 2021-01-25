<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

class EventLocation extends Model
{
    use UuidScopeTrait;

    protected $table='event_location';
    public $timestamps=true;
    public $incrementing=true;

    protected $dates=[
        'created_at',
        'updated_at'
    ];

    protected $fillable=[
        'uuid',
        'event_id',
        'location_id',
        'address',
        'show_map',
        'longitude',
        'latitude',
        'postal_code'
    ];

    public function volunteeringEvent() {
        return $this->belongsTo('App\VolunteeringEvents','event_id');
    }

    public function location() {
        return $this->belongsTo('App\Cities','location_id');
    }
}
