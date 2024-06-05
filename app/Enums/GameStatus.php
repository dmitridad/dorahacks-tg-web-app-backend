<?php

namespace App\Enums;

enum GameStatus: string
{
    case Created = 'created';
    case Started = 'started';
    case Finished = 'finished';
}
