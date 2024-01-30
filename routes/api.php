<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Chats\ChatController;
use App\Http\Controllers\Design\CommentController;
use App\Http\Controllers\Design\DesignController;
use App\Http\Controllers\Design\UploadController;
use App\Http\Controllers\Team\InvitationController;
use App\Http\Controllers\Team\TeamsController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('me', [MeController::class, 'getMe']);

// Get designs
Route::get('designs', [DesignController::class, 'index']);
Route::get('designs/{id}', [DesignController::class, 'show']);
Route::get('designs/slug/{id}', [DesignController::class, 'showBySlug']);

// Get Users
Route::get('users', [UserController::class, 'index']);
Route::get('user/{username}', [UserController::class, 'findByUsername']);
Route::get('users/{id}/designs', [DesignController::class, 'getForUser']);

Route::get('teams/slug/{slug}', [TeamsController::class, 'findBySlug']);
Route::get('teams/{id}/designs', [DesignController::class, 'getForTeam']);

// Search Designs
Route::get('search/designs', [DesignController::class, 'search']);
Route::get('search/designers', [UserController::class, 'search']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);

    // Upload Designs
    Route::post('designs', [UploadController::class, 'upload']);
    Route::put('designs/{id}', [DesignController::class, 'update']);
    Route::delete('designs/{id}', [DesignController::class, 'destroy']);

    // Likes
    Route::post('designs/{id}/like', [DesignController::class, 'like']);
    Route::get('designs/{id}/liked', [DesignController::class, 'checkIfUserHasLiked']);

    // Comments
    Route::post('designs/{id}/comments', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // Teams
    Route::post('teams', [TeamsController::class, 'store']);
    Route::get('teams', [TeamsController::class, 'index']);
    Route::get('users/teams', [TeamsController::class, 'fetchUserTeams']);
    Route::get('teams/{id}', [TeamsController::class, 'findById']);
    Route::put('teams/{id}', [TeamsController::class, 'update']);
    Route::delete('teams/{id}', [TeamsController::class, 'destroy']);
    Route::delete('teams/{team_id}/users/{user_id}', [TeamsController::class, 'removeFromTeam']);

    // Invitations
    Route::post('invitations/{teamId}', [InvitationController::class, 'invite']);
    Route::post('invitations/{id}/resend', [InvitationController::class, 'resend']);
    Route::post('invitations/{id}/respond', [InvitationController::class, 'respond']);
    Route::delete('invitations/{id}', [InvitationController::class, 'destroy']);

    // Chats

    Route::post('chats', [ChatController::class, 'sendMessage']);
    Route::get('chats', [ChatController::class, 'getUserChats']);
    Route::get('chats/{id}/messages', [ChatController::class, 'getChatMessages']);
    Route::put('chats/{id}/markAsRead', [ChatController::class, 'markAsRead']);
    Route::delete('messages/{id}', [ChatController::class, 'destroyMessage']);
});

Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')
        ->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
});
