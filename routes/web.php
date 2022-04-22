<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


//API
Route::post('e-doc/api/login', [AuthController::class, 'Login']);
Route::get('e-doc/api/user/acd_year', [UserController::class, 'Get_acd_year']);

//Vue
Route::any('/{any}', fn() => view('default'))->where('any', '.*');