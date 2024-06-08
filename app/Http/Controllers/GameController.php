<?php

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Models\Room;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    public function store(StoreGameRequest $request)
    {
        $roomId = $request->post('room_id');
        /* @var Room $room */
        $room = Room::query()->findOrFail($roomId);
        if ($room->activeGame()->exists()) {
            return response(['message' => 'Active game in the room'], Response::HTTP_CONFLICT);
        }

        return Game::query()->create([
            Game::PROP_ROOM_ID => $roomId,
            Game::PROP_GAME_STATUS => GameStatus::Created,
        ])->refresh();
    }
}
