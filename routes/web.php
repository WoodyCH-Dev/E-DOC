<?php

use Illuminate\Support\Facades\Route;

// Call API Route
Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));

// Show View (Vue 3)
Route::any('/{any}', fn() => view('default'))->where('any', '.*');
