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
    Route::get('/user/acd_year/lists', [UserController::class, 'Get_acd_year_all']);
    Route::post('/user/sync/google', [UserController::class, 'SyncWithGoogle']);

    //Sender
    Route::get('/sender/get/AllUserAndGroup', [UserController::class, 'Sender_Get_AlluserAndGroup']);
    Route::post('/sender/get/MySender', [UserController::class, 'Sender_Get_MySender']);
    Route::post('/sender/upload/files', [UserController::class, 'SenderUploadFiles']);
    Route::post('/sender/send/document', [UserController::class, 'SenderSendDocument']);

    //Admin
    Route::get('/admin/get/AllUser', [UserController::class, 'Get_Alluser']);
    Route::get('/admin/get/AllGroup', [UserController::class, 'Get_Allgroup']);
    Route::get('/admin/get/AllDocumentGroup', [UserController::class, 'Get_AllDocumentgroup']);
    Route::get('/admin/get/user/{user_id}', [UserController::class, 'AdminGetUser']);
    Route::post('/admin/create/User', [UserController::class, 'AdminAddUser']);
    Route::post('/admin/create/Group', [UserController::class, 'AdminAddGroup']);
    Route::post('/admin/create/DocumentGroup', [UserController::class, 'AdminAddDocumentGroup']);
    Route::post('/admin/edit/User', [UserController::class, 'AdminEditUser']);
    Route::post('/admin/edit/Group', [UserController::class, 'AdminEditGroup']);
    Route::post('/admin/edit/DocumentGroup', [UserController::class, 'AdminEditDocumentGroup']);
    Route::post('/admin/remove/User', [UserController::class, 'AdminRemoveUser']);
    Route::post('/admin/remove/Group', [UserController::class, 'AdminRemoveGroup']);
    Route::post('/admin/remove/DocumentGroup', [UserController::class, 'AdminRemoveDocumentGroup']);
    Route::post('/admin/edit/User/ResetSyncGoogle', [UserController::class, 'SyncWithGoogle']);
    Route::post('/admin/get/User/permission', [UserController::class, 'AdminGetUserPermission']);
    Route::post('/admin/edit/User/permission', [UserController::class, 'AdminEditUserPermission']);

    Route::get('/admin/get/Group/AllUser/{group_id}', [UserController::class, 'Get_Alluser_InGroup']);
    Route::post('/admin/edit/Group/User', [UserController::class, 'AdminEditGroupUser']);

    Route::post('/admin/get/AllSender', [UserController::class, 'Admin_Get_AllSender']);

});
