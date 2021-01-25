<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventAsset extends Pivot
{
    use UuidScopeTrait;

    protected $table = 'event_asset';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'event_id',
        'asset_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

}
