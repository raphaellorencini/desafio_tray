<?php

namespace App\Traits;

trait HasRolesTrait
{
    public function hasRole(...$roles)
    {
        foreach($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }
        return false;
    }
}
