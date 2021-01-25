<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VolunteerFavoriteOrganizations extends Pivot
{
    use UuidScopeTrait;

    protected $table = 'volunteer_favorite_organizations';
    public $timestamps=true;
    public $incrementing=true;

    protected $fillable = [
        'uuid',
        'volunteer_id',
        'organization_id'
    ];

    protected $dates=[
        'created_at',
        'updated_at'
    ];
}
