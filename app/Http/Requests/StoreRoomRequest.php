<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'room_name' => 'required|string',
        ];
    }
}
