<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface TeamInterface extends BaseInterface
{
    public function fetchUserTeams(): Collection;

    public function findBySlug(string $slug): void;
}
