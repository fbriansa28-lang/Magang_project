<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Attempt peserta per challenge (satu attempt per user per challenge).
 * total_score = nilai akhir skala 0-100 (SUM(skor) / SUM(bobot set) x 100).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->decimal('total_score', 6, 2)->default(0);
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();

            $table->unique(['user_id', 'challenge_id'], 'attempts_user_challenge_unique');
            $table->index(['challenge_id', 'status', 'total_score'], 'attempts_challenge_status_score_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
