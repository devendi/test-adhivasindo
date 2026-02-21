<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::get('/search/name', [SearchController::class, 'byName']);
    Route::get('/search/nim',  [SearchController::class, 'byNim']);
    Route::get('/search/ymd',  [SearchController::class, 'byYmd']);
});