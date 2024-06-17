<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    public const TABLE_NAME = 'room_users';

    public const PROP_ID = 'id';
    public const PROP_ROOM_ID = 'room_id';
    public const PROP_USER_ID = 'user_id';
    public const PROP_CREATED_AT = 'created_at';
    public const PROP_UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;
    protected $primaryKey = self::PROP_ID;

    protected $fillable = [
        self::PROP_ROOM_ID,
        self::PROP_USER_ID,
    ];
}
