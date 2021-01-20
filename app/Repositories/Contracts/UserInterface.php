<?php


namespace App\Repositories\Contracts;


interface UserInterface extends BaseInterface
{
    public function all();

    public function findByEmail(string $email);
}
