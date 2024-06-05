<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const TABLE_NAME = 'users';

    public const PROP_USER_ID = 'user_id';
    public const PROP_TG_USER_ID = 'tg_user_id';
    public const PROP_TG_FIRST_NAME = 'tg_first_name';
    public const PROP_TG_LAST_NAME = 'tg_last_name';
    public const PROP_TG_USERNAME = 'tg_username';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_USER_ID;

    protected $fillable = [
        self::PROP_TG_USER_ID,
        self::PROP_TG_FIRST_NAME,
        self::PROP_TG_LAST_NAME,
        self::PROP_TG_USERNAME,
    ];

    public function getAuthIdentifierName(): string
    {
        return self::PROP_TG_USER_ID;
    }

    // TODO change to another relation
    public function room(): BelongsToMany
    {
        return $this->belongsToMany(
            Room::class,
            RoomUser::TABLE_NAME,
            self::PROP_USER_ID,
            RoomUser::PROP_ROOM_ID,
            self::PROP_USER_ID,
            Room::PROP_ROOM_ID
        );
    }
}
