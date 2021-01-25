<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationCategories extends Pivot
{
    protected $table = 'organization_category';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable = [
        'organization_id',
        'category_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
