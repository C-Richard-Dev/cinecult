<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReconciliationRunStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reconciliation_runs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(ReconciliationRunStatus::RUNNING->value);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('last_page_processed')->default(1);
            $table->integer('movies_created')->default(0);
            $table->integer('candidates_created')->default(0);
            $table->integer('movies_reconciled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_runs');
    }
};
