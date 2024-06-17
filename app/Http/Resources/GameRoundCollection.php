<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GameRoundCollection extends ResourceCollection
{
    public $collects = GameRoundResource::class;
}
