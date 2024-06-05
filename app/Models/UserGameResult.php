<?php

namespace App\Models;

use App\Enums\UserGameResult as UserGameResultEnum;
use Illuminate\Database\Eloquent\Model;

class UserGameResult extends Model
{
    public const TABLE_NAME = 'user_game_results';

    public const PROP_ID = 'id';
    public const PROP_USER_ID = 'user_id';
    public const PROP_GAME_ID = 'game_id';
    public const PROP_GAME_RESULT = 'game_result';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_ID;

    protected $fillable = [
        self::PROP_USER_ID,
        self::PROP_GAME_ID,
        self::PROP_GAME_RESULT,
    ];

    protected $casts = [
        self::PROP_GAME_RESULT => UserGameResultEnum::class,
    ];
}
