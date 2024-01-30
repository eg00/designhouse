<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return \response()->json(['status' => $response], Response::HTTP_OK);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return \response()->json(['email' => $response], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
