<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{
    public function __construct(protected UserInterface $users)
    {
    }

    public function index(): ResourceCollection
    {
        $users = $this->users->withCriteria([
            new EagerLoad(['designs']),
        ])->all();

        return UserResource::collection($users);
    }

    public function search(Request $request): ResourceCollection
    {
        $designers = $this->users->search($request);

        return UserResource::collection($designers);
    }

    public function findByUsername(string $username): UserResource
    {
        $user = $this->users->findWhereFirst('username', $username);

        return new UserResource($user);
    }
}
