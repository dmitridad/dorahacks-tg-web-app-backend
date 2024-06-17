<?php

namespace App\Contexts;

use App\Models\User;

class CreateNewGame
{
    public function __construct(
        protected User $user,
        protected int $roomId,
        protected string $tonGameAddress,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getTonGameAddress(): string
    {
        return $this->tonGameAddress;
    }
}
