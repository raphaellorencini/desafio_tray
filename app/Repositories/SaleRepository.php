<?php

namespace App\Repositories;

class SaleRepository extends BaseRepository
{
    public function __construct() {
        $this->setModel('Sale');
    }
}
