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
        $lastRun = ReconciliationRun::query()->latest('id')->first();

        $startPage = $lastRun?->status === ReconciliationRunStatus::FAILED
            ? max(1, (int) $lastRun->last_page_processed)
            : (int) ($lastRun?->last_page_processed ?? 0) + 1;

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
