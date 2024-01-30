<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\TeamInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TeamRepository extends BaseRepository implements TeamInterface
{
    public function model(): Model
    {
        return new Team();
    }

    public function fetchUserTeams(): Collection
    {
        $user = auth()->user();
        assert($user instanceof User);

        return $user->teams()->get();
    }

    public function findBySlug(string $slug): void
    {
        // TODO: Implement findBySlug() method.
    }
}
