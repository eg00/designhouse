<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function attemptLogin(Request $request): bool
    {
        // attempt to issue a token
        $token = $this->guard()->attempt($this->credentials($request));

        if (! $token) {
            return false;
        }

        // get the authenticated user

        /** @var User $user */
        $user = $this->guard()->user();

        if (! $user->hasVerifiedEmail()) {
            return false;
        }

        // set the user`s token
        $this->guard()->setToken($token);

        return true;
    }

    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return \response()->json(['message' => 'Logged out sucessfully']);
    }

    protected function sendLoginResponse(Request $request): JsonResponse
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();

        //extract the expiry date of the token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration,
        ]);
    }

    protected function sendFailedLoginResponse(): JsonResponse
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json([
                'errors' => [
                    'verification' => 'You need to verify email account',
                ],
            ]);
        }
        throw ValidationException::withMessages([
            $this->username() => 'Authentication failed',
        ]);
    }
}
