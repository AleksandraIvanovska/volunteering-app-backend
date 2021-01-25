<?php

namespace App;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{
    use SoftDeletes, UuidScopeTrait;

    protected $table = 'comments';
    public $incrementing=true;
    public $timestamps=true;

    protected $fillable=[
        'uuid',
        'description',
        'creator_id',
        'user_id',
        'deleted_at'
    ];

    protected $dates=[
      'created_at',
      'updated_at',
      'deleted_at'
    ];

    public function creator() {
        return $this->belongsTo('App\User','creator_id');
    }

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }
}
