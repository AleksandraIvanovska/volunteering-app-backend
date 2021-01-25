<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Roles extends Model
{
    protected $table = 'roles';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'value',
        'uuid'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

//    public function users()
//    {
//        return $this->belongsToMany('App\User','user_role','role_id', 'user_id')->withTimestamps();
//    }
      public function users()
      {
          return $this->hasMany('App\User');
      }

}
