<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VolunteerEventAttendance extends Pivot
{
    use UuidScopeTrait;

    protected $table = 'volunteer_event_attendance';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'uuid',
        'volunteer_id',
        'event_id'
    ];

    protected $dates=[
        'created_at',
        'updated_at'
    ];
}
