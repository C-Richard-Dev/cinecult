<?php

namespace App\Jobs;

use App\Enums\ReconciliationRunStatus;
use App\Models\ReconciliationRun;
use App\Services\MovieReconciliationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Timeout;

#[Timeout(600)]
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
            'status' => ReconciliationRunStatus::RUNNING,
            'started_at' => now(),
            'last_page_processed' => $this->startPage,
        ]);

        $this->runId = $run->id;

        try {
            $movieReconciliationEngine->run($this->startPage, $run);

            $run->update([
                'status' => ReconciliationRunStatus::FINISHED,
                'finished_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $run->update([
                'status' => ReconciliationRunStatus::FAILED,
                'finished_at' => now(),
            ]);

            throw $exception;
        }
    }

    /**
     * Handles timeouts, when the worker kills the process and the catch
     * block in handle() never runs. The engine already persists progress
     * per page, so the run is simply marked as FINISHED — real
     * exceptions are already marked as FAILED by the catch block.
     */
    public function failed(?\Throwable $exception): void
    {
        if (! $this->runId) {
            return;
        }

        $run = ReconciliationRun::query()->find($this->runId);

        if (! $run || $run->status !== ReconciliationRunStatus::RUNNING) {
            return;
        }

        $run->update([
            'status' => ReconciliationRunStatus::FINISHED,
            'finished_at' => now(),
        ]);
    }
}
