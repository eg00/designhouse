<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use MatanYadaev\EloquentSpatial\Objects\Point;

class SettingsController extends Controller
{
    public function updateProfile(Request $request): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->update(array_merge($request->toArray(), compact('location')));

        return new UserResource($user);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', 'min:6', 'different:current_password'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password updated'], Response::HTTP_OK);
    }
}
