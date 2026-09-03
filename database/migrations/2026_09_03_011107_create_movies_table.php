<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\{MovieConciliationStatus, MovieCategory};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('conciliation_status', 20)->default(MovieConciliationStatus::Pending->value);
            $table->integer('category')->default(MovieCategory::Action->value);
            $table->string('archive_identifier')->unique();
            $table->integer('tmdb_id')->unique();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('overview')->nullable();
            $table->date('release_date')->nullable();
            $table->unsignedSmallInteger('runtime')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
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
