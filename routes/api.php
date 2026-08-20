<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\UserController;
  Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::post('/create', 'store');
    Route::get('/users/{id}', 'show');
    Route::put('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'destroy');
});
