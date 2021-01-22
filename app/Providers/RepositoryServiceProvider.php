<?php

namespace App\Providers;

use App\Repositories\Contracts\{ChatInterface,
    CommentInterface,
    DesignInterface,
    InvitationInterface,
    MessageInterface,
    TeamInterface,
    UserInterface
};
use App\Repositories\Eloquent\{ChatRepository,
    CommentRepository,
    DesignRepository,
    InvitationRepository,
    MessageRepository,
    TeamRepository,
    UserRepository
};
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
        $this->app->bind(InvitationInterface::class, InvitationRepository::class);
        $this->app->bind(ChatInterface::class, ChatRepository::class);
        $this->app->bind(MessageInterface::class, MessageRepository::class);
    }
}
