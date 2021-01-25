<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VolunteerLanguage extends Pivot
{
    use UuidScopeTrait;

    use UuidScopeTrait;
    protected $table = 'volunteer_languages';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable = [
      'uuid',
      'volunteer_id',
      'language_id',
      'level_id'
    ];

    protected $dates=[
      'created_at',
      'updated_at'
    ];

//    public function volunteer() {
//        return $this->belongsTo('App\Volunteer','volunteer_id');
//    }
//
//    public function language() {
//        return $this->belongsTo('App\Language','language_id');
//    }

    public function languageLevel() {
        return $this->belongsTo('App\LanguageLevel','level_id');
    }
}
