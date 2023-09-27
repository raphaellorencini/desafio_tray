<?php

namespace App\Repositories;

class RoleRepository extends BaseRepository
{
    public function __construct() {
        $this->setModel('Role');
    }

    public function getByName($name)
    {
        return $this->getModel()::where('name', $name)->first();
    }
}
