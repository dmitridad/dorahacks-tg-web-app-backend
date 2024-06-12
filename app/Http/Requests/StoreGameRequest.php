<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'room_id' => 'required|integer',
            'ton_game_address' => 'required|string',
        ];
    }
}
