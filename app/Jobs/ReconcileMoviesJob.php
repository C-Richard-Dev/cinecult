<?php

namespace App\Jobs;

use App\Enums\ReconciliationRunStatus;
use App\Models\ReconciliationRun;
use App\Services\MovieReconciliationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;

#[Timeout(120)]
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
        $startPage = max(1, (new ReconciliationRun)->the_last_page_processed);

        $run = ReconciliationRun::create([
            'status' => ReconciliationRunStatus::RUNNING,
            'started_at' => now(),
        ]);

        try {
            $lastPageProcessed = $movieReconciliationEngine->run($startPage);

            $run->update([
                'status' => ReconciliationRunStatus::FINISHED,
                'finished_at' => now(),
                'last_page_processed' => $lastPageProcessed,
            ]);
        } catch (\Throwable $exception) {
            $run->update([
                'status' => ReconciliationRunStatus::FAILED,
                'finished_at' => now(),
                'last_page_processed' => $startPage,
            ]);

            throw $exception;
        }
    }
}
