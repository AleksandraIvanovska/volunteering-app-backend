<?php

namespace App;

use App\Support\Extensions\CustomModel;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table='languages';
    public $timestamps=true;

    protected $fillable=[
        'language'
    ];

    protected $dates = [
      'created_at',
      'updated_at'
    ];

    public function volunteers() {
        return $this->belongsToMany('App\Volunteer','volunteer_languages', 'language_id','candidate_id')
            ->withPivot('level_id')
            ->join('language_level','level_id','language_level.value');
    }

}
