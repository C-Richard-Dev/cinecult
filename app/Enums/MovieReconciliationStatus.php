<?php

namespace App\Enums;

enum MovieReconciliationStatus: string
{
    case AUTOMATIC = 'automatic';
    case MANUAL = 'manual';
    case PENDING = 'pending';
}
