<?php

namespace App\Contexts;

use App\Models\User;

class JoinGame
{
    public function __construct(
        protected User $user,
        protected int $gameId
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getGameId(): int
    {
        return $this->gameId;
    }
}
