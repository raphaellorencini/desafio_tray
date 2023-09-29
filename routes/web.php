<?php

use App\Http\Controllers\Dashboard\IndexController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.login');
});
Route::get('/dashboard', [IndexController::class, 'dashboard'])->name('dashboard');
Route::get('/users', [IndexController::class, 'users'])->name('users');
Route::get('/sellers', [IndexController::class, 'sellers'])->name('sellers');
Route::get('/sales', [IndexController::class, 'sales'])->name('sales');
Route::post('/redirect', [IndexController::class, 'redirect'])->name('redirect');

Route::get('/logout', [IndexController::class, 'logout'])->name('logout2');

require __DIR__.'/auth.php';
