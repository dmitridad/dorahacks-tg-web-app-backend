<?php

namespace App\Http\Resources;

use App\Models\Room;
use App\Models\RoomUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * @var Room
     */
    public $resource;

    public function toArray(Request $request)
    {
        return [
            'room_id' => $this->resource->room_id,
            'room_name' => $this->resource->room_name,
            'room_capacity' => $this->resource->room_capacity,
            'room_status' => $this->resource->room_status,
            'users_count' => RoomUser::usersCount($this->resource->room_id),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
