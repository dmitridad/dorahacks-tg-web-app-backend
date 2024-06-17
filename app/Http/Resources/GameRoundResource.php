<?php

namespace App\Http\Resources;

use App\Models\GameRound;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameRoundResource extends JsonResource
{
    /**
     * @var GameRound
     */
    public $resource;

    public function toArray(Request $request)
    {
        return [
            'game_id' => $this->resource->game_id,
            'round_number' => $this->resource->round_number,
            'random_number' => $this->resource->random_number,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
