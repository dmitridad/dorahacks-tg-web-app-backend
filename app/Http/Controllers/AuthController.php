<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public const STATUS_CHECKED = 'checked';
    public const STATUS_AUTHENTICATED = 'authenticated';
    public const STATUS_REGISTERED = 'registered';

    public function getCsrfToken(Request $request): Response|JsonResponse
    {
        if ($request->expectsJson()) {
            return new JsonResponse(status: 204);
        }

        return new Response(status: 204);
    }

    public function authenticate(Request $request)
    {
        // check if the user has already been authenticated
        if (Auth::check()) {
            return response(['status' => self::STATUS_CHECKED]);
        }
        // check required header
        if (!$authHeader = $request->header('Authorization')) {
            return response()->noContent(403);
        }

        $position = strrpos($authHeader, 'tma ');

        // check required header data
        if ($position === false) {
            return response()->noContent(403);
        }

        $tgInitData = substr($authHeader, $position + 4);

        // TODO validate init data
        // TODO temporary solution
        $tgUserId = $tgInitData;

        // attempt to authenticate the user
        if (Auth::attempt(['tg_user_id' => $tgUserId])) {
            $request->session()->regenerate();

            return response(['status' => self::STATUS_AUTHENTICATED]);
        }

        // register new user and authenticate
        /* @var User $user */
        $user = User::query()->create([
            'tg_user_id' => $tgUserId,
        ]);

        Auth::login($user);

        return response(['status' => self::STATUS_REGISTERED]);
    }

    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
