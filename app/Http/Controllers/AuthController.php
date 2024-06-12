<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public const STATUS_CHECKED = 'checked';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_TOKEN_REGENERATED = 'regenerated';

    public function authenticate(Request $request)
    {
        $tgInitData = $request->attributes->get('tg_init_data');
        $tgUserData = json_decode($tgInitData['user'], true);
        $tgUserId = $tgUserData['id'];

        $user = User::fromTg($tgUserId)->first();
        if ($user) {
            return response(['status' => self::STATUS_CHECKED]);
        }

        // register new user and create token
        $token = DB::transaction(function () use ($tgUserData, $tgUserId){
            $firstName = $tgUserData['first_name'];
            $lastName = $tgUserData['last_name'] ?? null;

            /* @var User $user */
            $user = User::query()->create([
                User::PROP_TG_USER_ID => $tgUserId,
                User::PROP_TG_FIRST_NAME => $firstName,
                User::PROP_TG_LAST_NAME => $lastName,
                User::PROP_TG_USERNAME => $tgUserData['username'] ?? null,
            ]);

            return $user
                ->createToken($this->generateTokenName($firstName, $lastName))
                ->plainTextToken;
        });

        return response([
            'status' => self::STATUS_REGISTERED,
            'token' => $token,
        ]);
    }

    public function regenerateToken(Request $request)
    {
        $tgInitData = $request->attributes->get('tg_init_data');
        $tgUserData = json_decode($tgInitData['user'], true);
        $tgUserId = $tgUserData['id'];

        /* @var User $user */
        $user = User::fromTg($tgUserId)->firstOrFail();
        // delete the old token and create a new one
        $token = DB::transaction(function () use ($tgUserData, $user){
            $user->tokens()->delete();

            $firstName = $tgUserData['first_name'];
            $lastName = $tgUserData['last_name'] ?? null;

            return $user
                ->createToken($this->generateTokenName($firstName, $lastName))
                ->plainTextToken;
        });

        return response([
            'status' => self::STATUS_TOKEN_REGENERATED,
            'token' => $token,
        ]);
    }

    public function user()
    {
        return Auth::user();
    }

    public function deleteUser(Request $request)
    {
        $tgUserId = $request->input('tg_user_id');
        /* @var User $user */
        $user = User::fromTg($tgUserId)->firstOrFail();
        $user->tokens()->delete();
        $user->delete();

        return response(['status' => 'ok']);
    }

    protected function generateTokenName(string $firstName, ?string $lastName): string
    {
        $parts = array_filter([$firstName, $lastName, now()->timestamp]);

        return implode('_', $parts);
    }
}
