<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public const TABLE_NAME = 'games';

    public const PROP_GAME_ID = 'game_id';
    public const PROP_ROOM_ID = 'room_id';
    public const PROP_GAME_STATUS = 'game_status';
    public const PROP_TON_GAME_ADDRESS = 'ton_game_address';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_GAME_ID;

    protected $fillable = [
        self::PROP_ROOM_ID,
        self::PROP_GAME_STATUS,
        self::PROP_TON_GAME_ADDRESS,
    ];

    protected $casts = [
        self::PROP_GAME_STATUS => GameStatus::class,
    ];
}
