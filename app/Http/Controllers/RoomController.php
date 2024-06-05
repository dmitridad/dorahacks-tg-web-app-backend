<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomCollection;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use App\Services\RoomStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public function index()
    {
        return RoomCollection::make(Room::all());
    }

    public function store(StoreRoomRequest $request)
    {
        $roomName = $request->post('room_name');

        return Room::query()
            ->create([Room::PROP_ROOM_NAME => $roomName])
            ->refresh();
    }

    public function join(int $roomId)
    {
        /* @var User $user */
        $user = Auth::user();
        // TODO potentially problem with race condition
        /* @var Room $room */
        $room = Room::query()
            ->where(Room::PROP_ROOM_ID, $roomId)
            ->first();

        if (!$room) {
            return response(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }
        if ($room->room_status->isClosed()) {
            return response(['message' => 'Room is closed to join'], Response::HTTP_FORBIDDEN);
        }

        $roomUser = RoomUser::query()
            ->where(RoomUser::PROP_USER_ID, $user->user_id)
            ->first();

        if (!$roomUser) {
            /* @var RoomStatus $roomStatus */
            $roomStatus = app(RoomStatus::class);
            DB::transaction(function () use ($room, $user, $roomStatus){
                RoomUser::query()->create([
                    RoomUser::PROP_ROOM_ID => $room->room_id,
                    RoomUser::PROP_USER_ID => $user->user_id,
                ]);

                $roomStatus->sync($room);
            });

            return response(['message' => 'User is joined to the room'], Response::HTTP_OK);
        }

        if ($roomUser->room_id === $roomId) {
            return response(['message' => 'User is already in this room'], Response::HTTP_CONFLICT);
        } else {
            return response(['message' => 'User is in another room'], Response::HTTP_FORBIDDEN);
        }
    }

    public function leave()
    {
        /* @var User $user */
        $user = Auth::user();

        /* @var RoomStatus $roomStatus */
        $roomStatus = app(RoomStatus::class);
        $isDeleted = DB::transaction(function () use ($user, $roomStatus) {
            $room = $user->room()->first();
            if (!$room) {
                return false;
            }

            RoomUser::query()
                ->where(RoomUser::PROP_USER_ID, $user->user_id)
                ->delete();
            $roomStatus->sync($room);

            return true;
        });

        $message = $isDeleted ? 'leaved' : 'not joined';
        $status = $isDeleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return response(['status' => $message], $status);
    }
}
