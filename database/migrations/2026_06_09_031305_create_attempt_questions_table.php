<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot soal per attempt: weight disalin dari set challenge
 * (challenge_questions) saat attempt dibuat, sehingga tidak berubah walau
 * challenge disusun ulang kemudian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_banks')->restrictOnDelete();
            $table->enum('type', ['essay', 'pilihan_ganda']);
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->decimal('weight', 8, 2);
            $table->unsignedInteger('order_no');
            $table->text('answer')->nullable();
            $table->string('status', 20)->default('in_progress')->comment('in_progress/correct/wrong/time_expired');
            $table->unsignedInteger('wrong_count')->default(0)->comment('Jumlah percobaan salah (retry uraian)');
            $table->timestamp('hint_used_at')->nullable();
            $table->decimal('raw_score', 8, 4)->nullable()->comment('Skor murni sebelum penalti hint');
            $table->decimal('score', 8, 4)->nullable()->comment('Skor akhir setelah penalti hint');
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id'], 'attempt_questions_unique_question');
            $table->index(['attempt_id', 'order_no'], 'attempt_questions_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_questions');
    }
};
