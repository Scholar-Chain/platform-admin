<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('journals', [App\Http\Controllers\Api\JournalController::class, 'index']);
Route::get('journals/{journal}', [App\Http\Controllers\Api\JournalController::class, 'show']);

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
});


Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::group([
        'prefix' => 'auth'
    ], function ($router) {
        Route::put('profile', [App\Http\Controllers\Api\AuthController::class, 'edit']);
        Route::patch('profile', [App\Http\Controllers\Api\AuthController::class, 'edit']);
        Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
        Route::post('me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    });

    Route::group([
        'prefix' => 'author',
        'roles' => 'author'
    ], function ($router) {
        Route::post('submission', [App\Http\Controllers\Api\SubmissionController::class, 'store']);
    });
});
