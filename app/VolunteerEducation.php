<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

class VolunteerEducation extends Model
{
    use UuidScopeTrait;

    protected $table = 'volunteer_education';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable = [
        'uuid',
        'institution_name',
        'degree_name',
        'major',
        'start_date',
        'graduation_date',
        'volunteer_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function volunteer() {
        return $this->belongsTo('App\Volunteer','volunteer_id');
    }
}
