<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeController extends Controller
{
    public function getMe()
    {
        if(auth()->check()) {
//            return \response()->json(['user'=> auth()->user()], Response::HTTP_OK);
            return  new UserResource(auth()->user());
        }
        return \response()->json(null, Response::HTTP_UNAUTHORIZED);
}
}
