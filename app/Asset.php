<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\UuidScopeTrait;

class Asset extends Model
{
    use UuidScopeTrait;

    protected $table='assets';
    public $incrementing=true;
    public $timestamps=true;

    protected $hidden=['id'];

    protected $guarded = ['id'];
//    protected $fillable = [
//        'uuid',
//        'user_id',
//        'type',
//        'path',
//        'asset_name',
//        'mime'
//    ];

    public function organization() {
        return $this->belongsToMany('App\Organization', 'organization_asset','asset_id','organization_id')
            ->using('App\OrganizationAsset')
            ->withTimestamps();
    }

    public function volunteeringEvents() {
        return $this->belongsToMany('App\VolunteeringEvents', 'event_asset','asset_id','event_id')
            ->using('App\EventAsset')
            ->withTimestamps();
    }


    public function volunteer() {
        return $this->hasOne('App\Volunteer','cv');
    }
}
