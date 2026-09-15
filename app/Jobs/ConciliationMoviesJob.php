<?php

namespace App\Jobs;

use App\Services\MovieConciliationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ConciliationMoviesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(MovieConciliationEngine $movieConciliationEngine): void
    {
        $movieConciliationEngine->run();
    }
}
