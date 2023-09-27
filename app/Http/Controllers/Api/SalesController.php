<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SaleRepository;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct(
        protected SaleRepository $sales,
    ) {}

    public function index()
    {
        return $this->sales->getAll();
    }
}
