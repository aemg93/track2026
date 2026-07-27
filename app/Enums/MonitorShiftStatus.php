<?php

namespace App\Enums;

enum MonitorShiftStatus: string
{
    case Active = 'active';

    case Finished = 'finished';
}