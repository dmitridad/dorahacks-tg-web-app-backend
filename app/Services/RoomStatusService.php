<?php

namespace App\Services;

use App\Enums\GameStatus;
use App\Enums\RoomStatus;
use App\Models\Room;

class RoomStatusService
{
    /**
     * @throws \Exception
     */
    public static function sync(Room $room): void
    {
        $usersCount = $room->usersCount();

        if ($usersCount === 0) {
            $status = RoomStatus::Empty;
        } elseif ($usersCount < $room->room_capacity) {
            $status = RoomStatus::NotEmpty;
        } elseif ($usersCount === $room->room_capacity) {
            $status = RoomStatus::Full;
            if ($room->game->game_status === GameStatus::Started) {
                $status = RoomStatus::GameStarted;
            }
        }
        if (!$status) {
            throw new \Exception('Room status sync error');
        }

        $room->room_status = $status;
        $room->save();
    }
}
