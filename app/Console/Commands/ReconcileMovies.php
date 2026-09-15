<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileMoviesJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('movies:reconcile {--sync : Run the reconciliation job synchronously}')]
#[Description('Reconcile movies from Internet Archive with TMDB')]
class ReconcileMovies extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('sync')) {
            $this->components->info('Running movie reconciliation synchronously...');
            ReconcileMoviesJob::dispatchSync();
        } else {
            ReconcileMoviesJob::dispatch();
            $this->components->info('Movie reconciliation job dispatched successfully.');
        }
    }
}
