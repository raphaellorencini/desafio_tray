<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\UserControllerTrait;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class SellersController extends Controller
{
    use UserControllerTrait;

    public string $role = 'seller';

    public function __construct(
        protected UserRepository $users,
        protected RoleRepository $roles,
    ) {}

    public function index()
    {
        return $this->users->paginate('seller');
    }
}
