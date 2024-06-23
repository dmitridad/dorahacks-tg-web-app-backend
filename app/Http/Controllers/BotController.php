<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{
    public function webhook(Request $request)
    {
        $message = json_decode($request->post('message'), true);
        $text = $message['text'];

        $botToken = env('TG_BOT_TOKEN');
        $chatId = $message['chat']['id'];
        $messageText = 'Enjoy your game';
        $buttonText = "Play";
        $buttonUrl = env('TG_WEB_APP_URL');

        $url = env('TG_API_BASE_URL')."/bot$botToken/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $messageText,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => $buttonText, 'url' => $buttonUrl]
                    ]
                ]
            ])
        ];

        // TODO handle
        $response = Http::post($url, $data);

        return response()->noContent();
    }
}
