<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VolunteerEventInvitations extends Pivot
{
    use UuidScopeTrait;

    protected $table = 'volunteer_event_invitations';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'uuid',
        'volunteer_id',
        'event_id',
        'status_id',
        'status'
    ];

    protected $dates=[
        'created_at',
        'updated_at'
    ];

    public function statusType() {
        return $this->belongsTo('App\Resources','status_id');
    }
}
