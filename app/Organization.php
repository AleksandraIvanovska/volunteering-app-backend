<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\UuidScopeTrait;

class Organization extends Model
{
    use SoftDeletes,UuidScopeTrait;

    protected $table = 'organizations';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'name',
        'mission',
        'photo',
        'description',
        'location_id',
        'website',
        'facebook',
        'linkedIn',
        'phone_number',
        'user_id',
        'twitter',
        'instagram',
        'deleted_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    //USER
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    //LOCATION
    public function location() {
        return $this->belongsTo('App\Cities', 'location_id');
    }

    public function categories() {
        return $this->belongsToMany('App\Category','organization_category','organization_id','category_id')
            ->using('App\OrganizationCategories')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function contacts() {
        return $this->hasMany('App\Contact', 'organization_id');
    }

    public function assets() {
        return $this->belongsToMany('App\Asset','organization_asset','organization_id','asset_id')
            ->using('App\OrganizationAsset')
            ->withTimestamps();
    }

    public function volunteeringEvents() {
        return $this->hasMany('App\VolunteeringEvents','organization_id');
    }

    public function volunteerFavorites() {
        return $this->belongsToMany('App\Volunteer','volunteer_favorite_organizations','organization_id','volunteer_id')
            ->using('App\VolunteerFavoriteOrganizations')
            ->withTimestamps();
    }

}
