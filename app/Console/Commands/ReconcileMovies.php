<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileMoviesJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('movies:reconcile')]
#[Description('Reconcile movies from Internet Archive with TMDB')]
class ReconcileMovies extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ReconcileMoviesJob::dispatch();
        $this->components->info('Movie reconciliation job dispatched successfully.');
    }
}
