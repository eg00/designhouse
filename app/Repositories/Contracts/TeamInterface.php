<?php


namespace App\Repositories\Contracts;


interface TeamInterface extends BaseInterface
{
    public function fetchUserTeams();
    public function findBySlug($slug);
}
