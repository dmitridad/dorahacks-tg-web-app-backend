<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    public const TABLE_NAME = 'games';

    public const PROP_GAME_ID = 'game_id';
    public const PROP_ROOM_ID = 'room_id';
    public const PROP_GAME_STATUS = 'game_status';
    public const PROP_TON_GAME_ADDRESS = 'ton_game_address';
    public const PROP_GAME_CAPACITY = 'game_capacity';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_GAME_ID;

    protected $fillable = [
        self::PROP_ROOM_ID,
        self::PROP_GAME_STATUS,
        self::PROP_TON_GAME_ADDRESS,
        self::PROP_GAME_CAPACITY,
    ];

    protected $casts = [
        self::PROP_GAME_STATUS => GameStatus::class,
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(
            Room::class,
            Room::PROP_ROOM_ID,
            self::PROP_ROOM_ID
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            GameUser::TABLE_NAME,
            self::PROP_GAME_ID,
            User::PROP_USER_ID
        );
    }

    public function usersCount(): int
    {
        return $this->users()->count();
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(
            GameRound::class,
            GameRound::PROP_GAME_ID,
            self::PROP_GAME_ID
        );
    }

    public function currentRound(): HasOne
    {
        return $this->rounds()->one()->latestOfMany();
    }
}
