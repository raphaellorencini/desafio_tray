<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct(
        protected UserRepository $users,
    ) {}

    public function index()
    {
        return $this->users->getAll();
    }
}
