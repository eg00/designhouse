<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserInterface extends BaseInterface
{
    public function all();

    public function findByEmail(string $email);

    public function search(Request $request);
}
