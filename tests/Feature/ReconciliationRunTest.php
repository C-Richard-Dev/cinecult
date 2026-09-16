<?php

use App\Enums\ReconciliationRunStatus;
use App\Jobs\ReconcileMoviesJob;
use App\Models\ReconciliationRun;
use App\Services\MovieReconciliationEngine;

function createRun(ReconciliationRunStatus $status, int $lastPageProcessed): ReconciliationRun
{
    $run = new ReconciliationRun;
    $run->forceFill([
        'status' => $status,
        'started_at' => now(),
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

test('job resumes from the last processed page and saves the run as finished', function () {
    createRun(ReconciliationRunStatus::FINISHED, 7);

    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(7, Mockery::type(ReconciliationRun::class));

    (new ReconcileMoviesJob)->handle($engine);

    $run = ReconciliationRun::query()->latest('id')->first();

    expect($run->status)->toBe(ReconciliationRunStatus::FINISHED)
        ->and($run->last_page_processed)->toBe(7)
        ->and($run->started_at)->not->toBeNull()
        ->and($run->finished_at)->not->toBeNull();
});

test('job starts from the first page when there are no previous runs', function () {
    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(1, Mockery::type(ReconciliationRun::class));

    (new ReconcileMoviesJob)->handle($engine);

    expect(ReconciliationRun::query()->latest('id')->first()->status)->toBe(ReconciliationRunStatus::FINISHED);
});

test('job reprocesses the last page when the previous run failed', function () {
    createRun(ReconciliationRunStatus::FAILED, 4);

    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(4, Mockery::type(ReconciliationRun::class));

    (new ReconcileMoviesJob)->handle($engine);

    expect(ReconciliationRun::query()->latest('id')->first()->status)->toBe(ReconciliationRunStatus::FINISHED);
});

test('failed marks the run as finished keeping the interrupted page when the job times out', function () {
    createRun(ReconciliationRunStatus::FINISHED, 7);

    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(7, Mockery::type(ReconciliationRun::class));

    $job = new ReconcileMoviesJob;

    // Simulates the run being created before the job dies (e.g. timeout).
    $job->handle($engine);

    $run = ReconciliationRun::query()->latest('id')->first();
    $run->update(['status' => ReconciliationRunStatus::RUNNING, 'finished_at' => null]);

    $job->failed(null);

    $run->refresh();

    expect($run->status)->toBe(ReconciliationRunStatus::FINISHED)
        ->and($run->finished_at)->not->toBeNull()
        ->and($run->last_page_processed)->toBe(7);
});

test('failed does not override a run already marked as failed by the catch block', function () {
    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(1, Mockery::type(ReconciliationRun::class))->andThrow(new RuntimeException('API down'));

    $job = new ReconcileMoviesJob;

    try {
        $job->handle($engine);
    } catch (RuntimeException) {
        //
    }

    $job->failed(null);

    $run = ReconciliationRun::query()->latest('id')->first();

    expect($run->status)->toBe(ReconciliationRunStatus::FAILED);
});

test('failed does nothing when no run was created', function () {
    (new ReconcileMoviesJob)->failed(null);

    expect(ReconciliationRun::query()->count())->toBe(0);
});

test('job saves the run as failed when the engine throws', function () {
    createRun(ReconciliationRunStatus::FINISHED, 4);

    $engine = Mockery::mock(MovieReconciliationEngine::class);
    $engine->shouldReceive('run')->once()->with(4, Mockery::type(ReconciliationRun::class))->andThrow(new RuntimeException('API down'));

    try {
        (new ReconcileMoviesJob)->handle($engine);
        $this->fail('Expected RuntimeException to be thrown');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('API down');
    }

    $run = ReconciliationRun::query()->latest('id')->first();

    expect($run->status)->toBe(ReconciliationRunStatus::FAILED)
        ->and($run->last_page_processed)->toBe(4);
});
