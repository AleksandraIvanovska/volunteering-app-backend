<?php


namespace App\Support;

use App\User;

trait HasRoleTrait
{
    public function isVolunteer($user) {
        if(!$user->relationLoaded('role')) {
            $user->load('role');
        }
        if(!empty($user->role)) {
            if ($user->role->value === 'volunteer' || $user->role->value === 'administrator') {
                return true;
            }
        }
        return false;
    }

    public function isOrganization($user) {
        if(!$user->relationLoaded('role')) {
            $user->load('role');
        }
        if(!empty($user->role)) {
            if ($user->role->value === 'organization' || $user->role->value === 'administrator') {
                return true;
            }
        }
        return false;
    }

    public function isInternal($user) {
        if(!$user->relationLoaded('role')) {
            $user->load('role');
        }
        if(!empty($user->role)) {
            if ($user->role->value === 'volunteer' || $user->role->value === 'organization' || $user->role->value === 'administrator') {
                return true;
            }
        }
        return false;
    }

    public function isAdmin($user) {

    }
}
