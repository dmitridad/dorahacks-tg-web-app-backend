<?php

namespace App\Services;

use App\Contexts\CreateNewGame;
use App\Contexts\GetGameRoundsContext;
use App\Contexts\JoinGame;
use App\Enums\GameStatus;
use App\Exceptions\CustomRuntimeException;
use App\Models\Game;
use App\Models\GameRound;
use App\Models\GameUser;
use App\Models\Room;
use App\Models\User;
use App\Services\TON\SmartContracts\GameInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class GameService
{
    /**
     * @throws CustomRuntimeException
     */
    public function create(CreateNewGame $context): Game
    {
        /* @var Room $room */
        $room = Room::query()->findOrFail($context->getRoomId());
        if ($room->activeGame()->exists()) {
            throw new CustomRuntimeException(
                'Active game in the room',
                Response::HTTP_CONFLICT
            );
        }

        $this->throwIfRoomHasNoUser($room, $context->getUser());

        return DB::transaction(function () use ($context, $room) {
            $game = Game::query()->create([
                Game::PROP_ROOM_ID => $context->getRoomId(),
                Game::PROP_GAME_STATUS => GameStatus::Created,
                Game::PROP_TON_GAME_ADDRESS => $context->getTonGameAddress(),
                Game::PROP_GAME_CAPACITY => $room->room_capacity,
            ])->refresh();

            GameUser::query()->create([
                GameUser::PROP_GAME_ID => $game->game_id,
                GameUser::PROP_USER_ID => $context->getUser()->user_id,
            ]);

            return $game;
        });
    }

    /**
     * @throws CustomRuntimeException
     */
    public function join(JoinGame $context): bool
    {
        // TODO make sure that game and room statuses are correct after all actions
        /* @var Game $game */
        $game = Game::query()->findOrFail($context->getGameId());
        $room = $game->room;

        $this->throwIfRoomHasNoUser($room, $context->getUser());

        if (!$game->game_status->canJoin()) {
            throw new CustomRuntimeException(
                'Cannot join the game',
                Response::HTTP_FORBIDDEN
            );
        }

        $gameUsersCount = $game->usersCount();
        if ($gameUsersCount >= $game->game_capacity) {
            throw new CustomRuntimeException(
                'Game is closed to join',
                Response::HTTP_FORBIDDEN
            );
        }

        DB::transaction(function () use ($context, $room, $game, $gameUsersCount) {
            GameUser::query()->create([
                GameUser::PROP_GAME_ID => $game->game_id,
                GameUser::PROP_USER_ID => $context->getUser()->user_id,
            ]);

            // if the last joined user then create a ton request to get round data
            if ($gameUsersCount + 1 === $game->game_capacity) {
                /* @var GameInterface $gameSmartContract */
                $gameSmartContract = app(GameInterface::class, [
                    'address' => $game->ton_game_address,
                ]);

                // get last generated random number from the smart contract
                $lastNumber = $gameSmartContract->getLastNumber();
                // create first game round
                GameRound::query()->create([
                    GameRound::PROP_GAME_ID => $game->game_id,
                    GameRound::PROP_ROUND_NUMBER => 1,
                    GameRound::PROP_RANDOM_NUMBER => $lastNumber,
                ]);

                // start the game
                $game->game_status = GameStatus::Started;
                $game->save();

                RoomStatusService::sync($room);
            }
        });

        return true;
    }

    public function getGameRounds(GetGameRoundsContext $context): Collection
    {
        $this->throwIfRoomHasNoUser($context->getGame()->room, $context->getUser());

        return $context->getGame()->rounds;
    }

    /**
     * @throws CustomRuntimeException
     */
    protected function throwIfRoomHasNoUser(Room $room, User $user): void
    {
        if (!$room->hasUser($user)) {
            throw new CustomRuntimeException(
                'User is not in the room',
                Response::HTTP_FORBIDDEN
            );
        }
    }
}
