<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

class VolunteerExperience extends Model
{
    use UuidScopeTrait;

    protected $table = 'volunteer_experience';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'uuid',
        'volunteer_id',
        'job_title',
        'company_name',
        'location_id',
        'start_date',
        'end_date'
    ];

    protected $dates=[
      'created_at',
      'updated_at'
    ];

    public function volunteer() {
        return $this->belongsTo('App\Volunteer','volunteer_id');
    }

    public function location() {
        return $this->belongsTo('App\Cities','location_id');
    }


}
