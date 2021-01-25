<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventContact extends Pivot
{
    use UuidScopeTrait;

    protected $table='event_contact';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable=[
      'event_id',
      'contact_id'
    ];

    protected $dates=[
      'created_at',
      'updated_at'
    ];
}
