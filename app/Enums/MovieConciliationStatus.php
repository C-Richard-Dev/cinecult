<?php

namespace App\Enums;

enum MovieConciliationStatus: string
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';
    case PENDING = 'pending';
}
