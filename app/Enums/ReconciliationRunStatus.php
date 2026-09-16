<?php

namespace App\Enums;

enum ReconciliationRunStatus: string
{
    case RUNNING = 'running';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
