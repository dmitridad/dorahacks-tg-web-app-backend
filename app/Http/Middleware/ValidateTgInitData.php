<?php

namespace App\Http\Middleware;

use Closure;

class ValidateTgInitData
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check required header
        if (!$authHeader = $request->header('Authorization')) {
            return response(['message' => 'Authorization header required'], 403);
        }

        $position = strrpos($authHeader, 'tma ');

        // check required header data
        if ($position === false) {
            return response(['message' => 'tma authorization type required'], 403);
        }

        $tgInitDataString = substr($authHeader, $position + 4);
        if (!$tgInitDataString || !$this->isValidInitData($tgInitDataString)) {
            return response(['message' => 'Invalid init data'], 403);
        }


        parse_str($tgInitDataString, $tgInitData);
        $request->attributes->set('tg_init_data', $tgInitData);

        return $next($request);
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
