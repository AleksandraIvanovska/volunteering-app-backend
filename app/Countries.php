<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Countries extends Model
{
    protected $table='countries';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable=[
        'id',
        'name',
        'region',
        'sub_region',
        'latitude',
        'longitude'
    ];

    protected $dates =[
      'created_at',
      'updated_at'
    ];

    public function cities() {
        return $this->hasMany('App\Cities');
    }

    public function volunteer() {
        return $this->hasMany('App\Volunteer', 'nationality_id');
    }
}
