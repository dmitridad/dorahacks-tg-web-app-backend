<?php

namespace App\Http\Controllers;

use App\Contexts\CreateNewGame;
use App\Contexts\GetGameRoundsContext;
use App\Contexts\JoinGame;
use App\Exceptions\CustomRuntimeException;
use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameRoundCollection;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    public function store(StoreGameRequest $request)
    {
        $roomId = $request->post('room_id');
        $tonGameAddress = $request->post('ton_game_address');

        /* @var User $user */
        $user = Auth::user();
        $context = new CreateNewGame($user, $roomId, $tonGameAddress);

        /* @var GameService $gameService */
        $gameService = app(GameService::class);

        try {
            $game = $gameService->create($context);
        } catch (CustomRuntimeException $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }

        return $game;
    }

    public function join(int $gameId)
    {
        // TODO add validation for gameId
        /* @var User $user */
        $user = Auth::user();
        $context = new JoinGame($user, $gameId);

        /* @var GameService $gameService */
        $gameService = app(GameService::class);

        try {
            $gameService->join($context);
        } catch (CustomRuntimeException $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }

        return response(['message' => 'User is joined to the game'], Response::HTTP_OK);
    }

    public function getRounds(int $gameId)
    {
        /* @var User $user */
        $user = Auth::user();
        /* @var Game $game */
        $game = Game::query()->findOrFail($gameId);

        $context = new GetGameRoundsContext($user, $game);
        /* @var GameService $gameService */
        $gameService = app(GameService::class);

        try {
            return GameRoundCollection::make($gameService->getGameRounds($context))
                ->additional(['game_status' => $game->game_status]);
        } catch (CustomRuntimeException $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }
}
