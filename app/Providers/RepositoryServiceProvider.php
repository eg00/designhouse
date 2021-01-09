<?php

namespace App\Providers;

use App\Repositories\Contracts\{CommentInterface, DesignInterface, TeamInterface, UserInterface};
use App\Repositories\Eloquent\{CommentRepository, DesignRepository, TeamRepository, UserRepository};
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(DesignInterface::class, DesignRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CommentInterface::class, CommentRepository::class);
        $this->app->bind(TeamInterface::class, TeamRepository::class);
    }
}
