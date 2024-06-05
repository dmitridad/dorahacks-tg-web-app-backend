<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomUser;

class RoomStatus
{
    public function sync(Room $room): void
    {
        $usersCount = RoomUser::usersCount($room->room_id);
        if ($usersCount === 0) {
            $status = \App\Enums\RoomStatus::Empty;
        } elseif ($usersCount < $room->room_capacity) {
            $status = \App\Enums\RoomStatus::NotEmpty;
        } elseif ($usersCount === $room->room_capacity) {
            // TODO add condition for GameStarted
            $status = \App\Enums\RoomStatus::Full;
        }
        if (!$status) {
            throw new \Exception('Room status sync error');
        }

        $room->room_status = $status;
        $room->save();
    }
}
