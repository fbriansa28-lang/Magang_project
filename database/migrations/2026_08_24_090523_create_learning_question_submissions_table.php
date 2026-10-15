<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_question_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_progress_id')->constrained('learning_progresses')->cascadeOnDelete();
            $table->foreignId('learning_question_id')->constrained('learning_questions')->cascadeOnDelete();
            $table->text('submitted_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->unique(['learning_progress_id', 'learning_question_id'], 'lqs_progress_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_question_submissions');
    }
};
