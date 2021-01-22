<?php


namespace App\Repositories\Contracts;


interface ChatInterface extends BaseInterface
{

    public function createParticipants($chat_id, array $data);
}
