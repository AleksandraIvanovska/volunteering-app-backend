<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

class EventRequirements extends Model
{
    use UuidScopeTrait;

    protected $table='event_requirements';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable=[
        'uuid',
        'event_id',
        'driving_license',
        'minimum_age',
        'languages',
        'orientation',
        'background_check',
        'other'
    ];

    protected $dates=[
       'created_at',
       'updated_at'
    ];

    public function volunteeringEvent() {
        return $this->belongsTo('App\VolunteeringEvents','event_id');
    }
}
