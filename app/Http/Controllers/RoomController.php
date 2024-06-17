<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomCollection;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use App\Services\RoomStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    public function index()
    {
        return RoomCollection::make(Room::all());
    }

    public function show(int $roomId)
    {
        $room = Room::query()->findOrFail($roomId);

        return RoomResource::make($room);
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
        if ($room->room_status !== RoomStatus::Empty && $room->activeGame()->doesntExist()) {
            return response(['message' => 'Game is not created, try again later'], Response::HTTP_CONFLICT);
        }

        $roomUser = RoomUser::query()
            ->where(RoomUser::PROP_USER_ID, $user->user_id)
            ->first();

        if (!$roomUser) {
            DB::transaction(function () use ($room, $user){
                RoomUser::query()->create([
                    RoomUser::PROP_ROOM_ID => $room->room_id,
                    RoomUser::PROP_USER_ID => $user->user_id,
                ]);

                RoomStatusService::sync($room);
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
        $isDeleted = DB::transaction(function () use ($user) {
            $room = $user->room()->first();
            if (!$room) {
                return false;
            }

            RoomUser::query()
                ->where(RoomUser::PROP_USER_ID, $user->user_id)
                ->delete();
            RoomStatusService::sync($room);

            return true;
        });

        $message = $isDeleted ? 'leaved' : 'not joined';
        $status = $isDeleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return response(['status' => $message], $status);
    }
}
