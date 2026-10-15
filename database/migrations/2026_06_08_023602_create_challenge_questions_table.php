<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Set soal tetap per challenge: dipilih SEKALI saat admin menyimpan
 * challenge (acak, lalu permanen) dan dipakai SAMA untuk semua peserta.
 * Bobot mengikuti tipe x tingkat soal (PG 1/2/3, Uraian 3/5/8).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained('challenges')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_banks')->restrictOnDelete();
            $table->enum('type', ['essay', 'pilihan_ganda']);
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->decimal('weight', 8, 2);
            $table->unsignedInteger('order_no');
            $table->timestamps();

            $table->unique(['challenge_id', 'question_id'], 'challenge_questions_unique_question');
            $table->index(['challenge_id', 'order_no'], 'challenge_questions_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_questions');
    }
};
