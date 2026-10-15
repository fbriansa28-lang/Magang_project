<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_question_id')
                  ->constrained('learning_questions')
                  ->cascadeOnDelete();
            $table->enum('option_key', ['a', 'b', 'c', 'd', 'e']);
            $table->string('option_text');
            $table->timestamps();

            $table->unique(['learning_question_id', 'option_key'], 'learning_question_options_question_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_question_options');
    }
};
