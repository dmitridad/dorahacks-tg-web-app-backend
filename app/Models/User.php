<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const PROP_TG_USER_ID = 'tg_user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::PROP_TG_USER_ID,
    ];

    public function getAuthIdentifierName(): string
    {
        return self::PROP_TG_USER_ID;
    }
}
