<?php

namespace App\Models;

use App\Enums\GameStatus;
use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    public const TABLE_NAME = 'rooms';

    public const PROP_ROOM_ID = 'room_id';
    public const PROP_ROOM_NAME = 'room_name';
    public const PROP_ROOM_CAPACITY = 'room_capacity';
    public const PROP_ROOM_STATUS = 'room_status';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_ROOM_ID;

    protected $fillable = [
        self::PROP_ROOM_NAME,
        self::PROP_ROOM_CAPACITY,
        self::PROP_ROOM_STATUS,
    ];

    protected $casts = [
        self::PROP_ROOM_STATUS => RoomStatus::class,
    ];

    public function activeGame(): HasOne
    {
        return $this
            ->hasOne(Game::class, Game::PROP_ROOM_ID, self::PROP_ROOM_ID)
            ->where(Game::PROP_GAME_STATUS, '!=', GameStatus::Finished);
    }

    public function hasUser(User $user): bool
    {
        return RoomUser::query()
            ->where(RoomUser::PROP_ROOM_ID, $this->room_id)
            ->where(RoomUser::PROP_USER_ID, $user->user_id)
            ->exists();
    }

    public function game(): HasOne
    {
        return $this->hasOne(Game::class, Game::PROP_ROOM_ID, self::PROP_ROOM_ID);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            RoomUser::TABLE_NAME,
            self::PROP_ROOM_ID,
            User::PROP_USER_ID
        );
    }

    public function usersCount(): int
    {
        return $this->users()->count();
    }
}
