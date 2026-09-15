<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MovieConciliationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('archive_identifier')->unique();
            $table->integer('tmdb_id')->unique()->nullable();
            $table->string('conciliation_status', 50)->default(MovieConciliationStatus::PENDING->value);
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('overview')->nullable();
            $table->date('release_date')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('video_file_name')->nullable();
            $table->date('conciliation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
