<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('test', function () {
            return rand();
        })->middleware(['role:admin']);
        Route::get('users', [UsersController::class, 'index']);
        Route::get('sales', [SalesController::class, 'index']);
    });

    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('logout', [AuthenticationController::class, 'logout'])->middleware('auth:api');
});
