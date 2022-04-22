<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


//API
Route::post('e-doc/api/login', [AuthController::class, 'Login']);

//Vue
Route::any('/{any}', fn() => view('default'))->where('any', '.*');