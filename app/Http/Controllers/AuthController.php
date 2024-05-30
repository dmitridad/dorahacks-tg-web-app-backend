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
            return response(['error' => 'Authorization header required'], 403);
        }

        $position = strrpos($authHeader, 'tma ');

        // check required header data
        if ($position === false) {
            return response(['error' => 'tma authorization type required'], 403);
        }

        $tgInitDataString = substr($authHeader, $position + 4);
        if (!$tgInitDataString || !$this->isValidInitData($tgInitDataString)) {
            return response(['error' => 'Invalid init data'], 403);
        }

        parse_str($tgInitDataString, $tgInitData);
        $tgUserData = json_decode($tgInitData['user'], true);
        $tgUserId = $tgUserData['id'];

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

    protected function isValidInitData(string $initData): bool
    {
        [$checksum, $sortedInitData] = $this->convertInitData($initData);
        $secretKey = hash_hmac('sha256', env('TG_BOT_TOKEN'), 'WebAppData', true);
        $hash = bin2hex(hash_hmac('sha256', $sortedInitData, $secretKey, true));

        return 0 === strcmp($hash, $checksum);
    }

    protected function convertInitData(string $initData): array
    {
        // TODO check auth date not expired
        $initDataArray = explode('&', rawurldecode($initData));
        $needle = 'hash=';
        $hash = '';

        foreach ($initDataArray as &$data) {
            if (str_starts_with($data, $needle)) {
                $hash = substr_replace($data, '', 0, \strlen($needle));
                $data = null;
            }
        }

        $initDataArray = array_filter($initDataArray);
        sort($initDataArray);

        return [$hash, implode("\n", $initDataArray)];
    }
}
