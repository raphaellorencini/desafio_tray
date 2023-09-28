<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct(protected UserRepository $userRepository)
    { }

    public function auth()
    {
        if(!session('auth')) {
            abort(401);
        }
    }

    public function session() {
        $session = session()->all();
        return [
            'token' => $session['token'],
            'user_id' => $session['user_id'],
            'user_name' => $session['user_name'],
        ];
    }

    public function dashboard(Request $request)
    {
        $this->auth();

        return view('pages.dashboard', $this->session());
    }

    public function users(Request $request)
    {
        $this->auth();

        return view('pages.users', $this->session());
    }

    public function sellers(Request $request)
    {
        $this->auth();

        return view('pages.sellers', $this->session());
    }

    public function sales(Request $request)
    {
        $this->auth();

        return view('pages.sales', $this->session());
    }

    public function redirect(Request $request)
    {
        $user_id = $request->get('id');
        $user = $this->userRepository->getById($user_id);
        $request->session()->put('token', $request->get('token'));
        $request->session()->put('user_id', $user_id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('auth', true);
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth');
        return redirect('/');
    }
}
