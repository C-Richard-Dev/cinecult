<?php

namespace App\Enums;

enum CompatibilityLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case GOOD = 'good';
    case HIGH = 'high';
}