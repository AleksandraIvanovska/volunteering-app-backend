<?php

namespace App;

use App\Support\Extensions\CustomModel;
use Illuminate\Database\Eloquent\Model;

class LanguageLevel extends Model
{
    protected $table='language_level';
    public $incrementing=true;

    protected $fillable=[
        'uuid',
        'value',
        'description',
        'european_framework'
    ];

    protected $dates=[
        'created_at',
        'updated_at'
        ];

    public function languages() {
        return $this->hasMany('App\VolunteerLanguage','level_id');
    }

}
