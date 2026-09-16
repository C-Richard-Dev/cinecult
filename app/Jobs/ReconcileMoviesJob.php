<?php

namespace App\Jobs;

use App\Enums\ReconciliationRunStatus;
use App\Models\ReconciliationRun;
use App\Services\MovieReconciliationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;

#[Timeout(300)]
class ReconcileMoviesJob implements ShouldQueue
{
    use Queueable;

    public ?int $runId = null;

    public int $startPage = 1;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(MovieReconciliationEngine $movieReconciliationEngine): void
    {
        $this->startPage = max(1, (new ReconciliationRun)->the_last_page_processed);

        $run = ReconciliationRun::create([
            'last_page_processed' => $this->startPage,
        ]);

        try {
            $lastPageProcessed = $movieReconciliationEngine
                ->run($this->startPage, $run->id);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
