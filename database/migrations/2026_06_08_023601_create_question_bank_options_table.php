<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')
                  ->constrained('question_banks')
                  ->cascadeOnDelete();
            $table->enum('option_key', ['a', 'b', 'c', 'd', 'e']);
            $table->string('option_text');
            $table->timestamps();

            $table->unique(['question_bank_id', 'option_key'], 'question_bank_options_bank_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_options');
    }
};
