<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\MeController;
use Illuminate\Support\Facades\Route;

Route::get('me', [MeController::class, 'getMe']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
});


Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')
        ->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', [LoginController::class, 'login']);
});
