<?php

use App\Enums\ReconciliationRunStatus;
use App\Models\ReconciliationRun;

function createRun(ReconciliationRunStatus $status, int $lastPageProcessed): ReconciliationRun
{
    $run = new ReconciliationRun;
    $run->forceFill([
        'status' => $status,
        'last_page_processed' => $lastPageProcessed,
    ])->save();

    return $run;
}

test('the last page processed returns the value from the most recent run regardless of status', function () {
    createRun(ReconciliationRunStatus::FINISHED, 5);
    createRun(ReconciliationRunStatus::FAILED, 8);
    createRun(ReconciliationRunStatus::RUNNING, 12);

    expect(ReconciliationRun::query()->first()->the_last_page_processed)->toBe(12);
});

test('the last page processed returns zero when there are no runs', function () {
    expect((new ReconciliationRun)->the_last_page_processed)->toBe(0);
});
