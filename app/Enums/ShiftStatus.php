<?php

namespace App\Enums;

enum ShiftStatus: string
{
    case Active = 'active';

    case Paused = 'paused';

    case Finished = 'finished';
}