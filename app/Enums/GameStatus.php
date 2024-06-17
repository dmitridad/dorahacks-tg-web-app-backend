<?php

namespace App\Enums;

enum GameStatus: string
{
    case Created = 'created';
    case Started = 'started';
    case Finished = 'finished';

    public function canJoin(): bool
    {
        return match($this) {
            GameStatus::Created => true,
            GameStatus::Started, GameStatus::Finished => false,
        };
    }
}
