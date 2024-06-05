<?php

namespace App\Enums;

enum UserGameResult: string
{
    case Win = 'win';
    case Loss = 'loss';
    case Draw = 'draw';
}
