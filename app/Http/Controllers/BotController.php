<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public function webhook(Request $request)
    {
        $message = $request->post('message');
        $botToken = env('TG_BOT_TOKEN');
        $sendMessageUrl = env('TG_API_BASE_URL')."/bot$botToken/sendMessage";

        $data = [
            'chat_id' => $message['chat']['id'],
            'text' => env('TG_WEB_APP_PLAY_MSG'),
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => env('TG_WEB_APP_PLAY_BTN_TXT'),
                            'web_app' => ['url' => env('TG_WEB_APP_URL')]
                        ],
                    ],
                ],
            ])
        ];

        $response = Http::post($sendMessageUrl, $data);
        $decodedResponse = $response->json();
        if (!$decodedResponse['ok']) {
            $errorMsg = 'tg sendMessage error';
            Log::error($errorMsg, [
                'data' => $data,
                'url' => $sendMessageUrl,
                'response' => $decodedResponse,
            ]);
        }

        return response()->noContent();
    }
}
