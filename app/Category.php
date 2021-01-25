<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'value',
        'description',
        'photo'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function organizations() {
        return $this->belongsToMany('App\Organization','organization_category','category_id','organization_id')
            ->using('App\OrganizationCategories')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function volunteeringEvent() {
        return $this->hasMany('App\VolunteeringEvents','category_id');
    }
}
