<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;

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
}
