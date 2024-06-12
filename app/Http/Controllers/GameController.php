<?php

namespace App\Http\Controllers;

use App\Enums\GameStatus;
use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    public function store(StoreGameRequest $request)
    {
        $roomId = $request->post('room_id');
        $tonGameAddress = $request->post('ton_game_address');

        /* @var Room $room */
        $room = Room::query()->findOrFail($roomId);
        if ($room->activeGame()->exists()) {
            return response(['message' => 'Active game in the room'], Response::HTTP_CONFLICT);
        }

        /* @var User $user */
        $user = Auth::user();
        if (!$room->hasUser($user)) {
            return response(
                ['message' => 'User is not in the room'],
                Response::HTTP_FORBIDDEN
            );
        }

        return Game::query()->create([
            Game::PROP_ROOM_ID => $roomId,
            Game::PROP_GAME_STATUS => GameStatus::Created,
            Game::PROP_TON_GAME_ADDRESS => $tonGameAddress,
        ])->refresh();
    }
}
