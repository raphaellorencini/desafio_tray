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

    public function index(Request $request)
    {
        $sellerId = $request->get('seller_id');
        return $this->sales->paginate($sellerId);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
