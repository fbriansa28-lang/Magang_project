<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_material_id')->constrained('learning_materials')->cascadeOnDelete();
            $table->text('question');
            $table->string('image_path')->nullable();
            $table->enum('question_type', ['pilihan_ganda', 'uraian'])->default('pilihan_ganda');
            $table->enum('correct_option', ['a', 'b', 'c', 'd', 'e'])->nullable();
            $table->text('answer_key')->nullable()->comment('Kunci jawaban acuan untuk tipe uraian');
            $table->text('hint')->nullable();
            $table->unsignedSmallInteger('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_questions');
    }
};