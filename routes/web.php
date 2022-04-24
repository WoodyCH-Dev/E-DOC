<?php

use Illuminate\Support\Facades\Route;

//Vue
Route::any('/{any}', fn() => view('default'))->where('any', '.*');