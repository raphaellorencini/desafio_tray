<?php

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    public function __construct() {
        $this->setModel('User');
    }
}
