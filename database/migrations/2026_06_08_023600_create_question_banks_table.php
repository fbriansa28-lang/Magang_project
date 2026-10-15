<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                  ->nullable()
                  ->constrained('kategori')
                  ->nullOnDelete();
            $table->string('group_id', 64)->nullable()->comment('Soal dengan group_id sama = satu paket soal');
            $table->text('content')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('question_type', ['essay', 'pilihan_ganda'])->default('essay');
            $table->string('flag', 500)->nullable();
            $table->enum('correct_option', ['a', 'b', 'c', 'd', 'e'])->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->text('hint')->nullable();
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index(['difficulty', 'is_active']);
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
