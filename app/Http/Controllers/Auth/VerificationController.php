<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */


    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $users;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserInterface $users)
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users = $users;
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function verify(Request $request, User $user): JsonResponse
    {
        // check if the url is a valid signed url
        if (!URL::hasValidSignature($request)) {
            return \response()->json(
                [
                    "errors" => [
                        "message" => "Invalid verification link"
                    ]
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // check if the user has already verified  account
        if ($user->hasVerifiedEmail()) {
            return \response()->json(
                [
                    "errors" => [
                        "message" => "Email address already verified"
                    ]
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        if ($user->markEmailAsVerified()) {
            event(new Verified($request->user()));
            return \response()->json(["message" => "Email successfully verified"], Response::HTTP_OK);
        }


        return \response()->json(
            [
                "errors" => [
                    "message" => "Unknown Error"
                ]
            ],
            520
        );
    }

    /**
     * Resend the email verification notification.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $this->validate($request, ['email' => ['email', 'required']]);

        $user = $this->users->findWhereFirst('email', $request->email);
        if (!$user) {
            return \response()->json([
                "errors" => [
                    "message" => "No user could be found with this email address"
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // check if the user has already verified  account
        if ($user->hasVerifiedEmail()) {
            return \response()->json([
                "errors" => [
                    "message" => "Email address already verified"
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user->sendEmailVerificationNotification();

        return \response()->json(['status' => "verification link resent"]);
    }
}
