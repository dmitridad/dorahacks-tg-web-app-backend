<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomUser;

class RoomStatusService
{
    /**
     * @throws \Exception
     */
    public function sync(Room $room): void
    {
        $usersCount = RoomUser::usersCount($room->room_id);
        if ($usersCount === 0) {
            $status = RoomStatus::Empty;
        } elseif ($usersCount < $room->room_capacity) {
            $status = RoomStatus::NotEmpty;
        } elseif ($usersCount === $room->room_capacity) {
            // TODO add condition for GameStarted
            $status = RoomStatus::Full;
        }
        if (!$status) {
            throw new \Exception('Room status sync error');
        }

        $room->room_status = $status;
        $room->save();
    }
}
