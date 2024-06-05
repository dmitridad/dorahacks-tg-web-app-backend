<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Empty = 'empty';
    case NotEmpty = 'not_empty';
    case Full = 'full';
    case GameStarted = 'game_started';

    public function isClosed(): bool
    {
        return match($this) {
            RoomStatus::Empty, RoomStatus::NotEmpty => false,
            RoomStatus::Full, RoomStatus::GameStarted => true,
        };
    }
}
