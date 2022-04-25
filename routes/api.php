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
Route::post('/loginwithgoogle', [AuthController::class, 'LoginWithGoogle']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('/refresh', [AuthController::class, 'refresh']);
    //User
    Route::get('/user/dashboard/getusercount', [UserController::class, 'GetUserCount']);
    Route::get('/user/acd_year', [UserController::class, 'Get_acd_year']);
    Route::post('/user/sync/google', [UserController::class, 'SyncWithGoogle']);

    //Sender

    //Admin
    Route::get('/admin/get/AllUser', [UserController::class, 'Get_Alluser']);
    Route::get('/admin/get/user/{user_id}', [UserController::class, 'AdminGetUser']);
    Route::post('/admin/create/User', [UserController::class, 'AdminAddUser']);
    Route::post('/admin/edit/User', [UserController::class, 'AdminEditUser']);
    Route::post('/admin/remove/User', [UserController::class, 'AdminRemoveUser']);
    Route::post('/admin/edit/User/ResetSyncGoogle', [UserController::class, 'SyncWithGoogle']);
    Route::post('/admin/get/User/permission', [UserController::class, 'AdminGetUserPermission']);
    Route::post('/admin/edit/User/permission', [UserController::class, 'AdminEditUserPermission']);
});