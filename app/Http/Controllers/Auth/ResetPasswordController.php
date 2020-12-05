<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    protected function sendResetResponse(Request $request, $response)
    {
        return \response()->json(['status' => $response], Response::HTTP_OK);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return \response()->json(['email' => $response], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
