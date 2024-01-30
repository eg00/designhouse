<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ChatInterface extends BaseInterface
{
    /**
     * @param  array<mixed>  $data
     */
    public function createParticipants(int $chat_id, array $data): void;

    public function getUserChats(): Collection;
}
