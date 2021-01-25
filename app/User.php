<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_organization', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Roles');
    }

    public function organization()
    {
        return $this->hasOne('App\Organization', 'user_id');
    }

    public function volunteer()
    {
        return $this->hasOne('App\Volunteer','user_id');
    }

    public function commentCreator() {
        return $this->hasMany('App\Comments','creator_id');
    }

    public function commentReceiver() {
        return $this->hasMany('App\Comments','user_id');
    }

    public function eventsOwner()
    {
        return $this->hasMany('App\Events', 'sender_id');
    }

    public function events()
    {
        return $this->belongsToMany('App\Events', 'event_user', 'user_id', 'event_id')->withPivot('is_read', 'read_time')->withTimestamps();
    }
}
