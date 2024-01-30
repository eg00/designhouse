<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface UserInterface extends BaseInterface
{
    public function all(): Collection;

    public function findByEmail(string $email): ?User;

    public function search(Request $request): Collection;

    /**
     * @param  array<mixed>  $criteria
     */
    public function withCriteria(...$criteria): self;
}
