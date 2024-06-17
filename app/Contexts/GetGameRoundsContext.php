<?php

namespace App\Contexts;

use App\Models\Game;
use App\Models\User;

class GetGameRoundsContext
{
    public function __construct(
        protected User $user,
        protected Game $game
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getGame(): Game
    {
        return $this->game;
    }
}
