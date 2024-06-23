<?php

namespace App\Http\Resources;

use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RoomResource extends JsonResource
{
    /**
     * @var Room
     */
    public $resource;

    public function toArray(Request $request)
    {
        /* @var User $user */
        $user = Auth::user();
        $activeGame = $this->resource->activeGame()->first();
        $usersInGame = $activeGame ? $activeGame->usersCount() : 0;

        return [
            'room_id' => $this->resource->room_id,
            'room_name' => $this->resource->room_name,
            'room_capacity' => $this->resource->room_capacity,
            'room_status' => $this->resource->room_status,
            'users_count' => $this->resource->usersCount(),
            'users_in_game_count' => $usersInGame,
            'auth_user_in_room' => $this->resource->hasUser($user),
            'game' => $activeGame,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
