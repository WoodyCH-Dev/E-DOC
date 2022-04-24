<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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


//API
Route::post('/login', [AuthController::class, 'Login']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('/refresh', [AuthController::class, 'refresh']);
    //User
    Route::get('/user/acd_year', [UserController::class, 'Get_acd_year']);
    //Sender

    //Admin
    Route::get('/admin/get/AllUser', [UserController::class, 'Get_Alluser']);
});
