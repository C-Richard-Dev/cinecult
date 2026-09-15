<?php

namespace App\Jobs;

use App\Services\MovieReconciliationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReconcileMoviesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(MovieReconciliationEngine $movieReconciliationEngine): void
    {
        $movieReconciliationEngine->run();
    }
}
