<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('journals', [App\Http\Controllers\Api\JournalController::class, 'index']);
Route::get('journals/{journal}', [App\Http\Controllers\Api\JournalController::class, 'show']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
    Route::post('me', [App\Http\Controllers\Api\AuthController::class, 'me']);
});
