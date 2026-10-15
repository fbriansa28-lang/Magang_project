<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->integer('duration_minutes')->default(0)->comment('Total duration in minutes');
            $table->unsignedInteger('questions_easy')->default(0)->comment('Jumlah soal tingkat mudah (split otomatis PG/Uraian)');
            $table->unsignedInteger('questions_medium')->default(0)->comment('Jumlah soal tingkat sedang (split otomatis PG/Uraian)');
            $table->unsignedInteger('questions_hard')->default(0)->comment('Jumlah soal tingkat sulit (split otomatis PG/Uraian)');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};