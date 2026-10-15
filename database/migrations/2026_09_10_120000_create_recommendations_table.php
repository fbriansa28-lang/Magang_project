<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->foreignId('learning_material_id')->nullable()->constrained('learning_materials')->nullOnDelete();
            $table->foreignId('challenge_id')->nullable()->constrained('challenges')->nullOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('question_banks')->nullOnDelete();
            $table->unsignedTinyInteger('average_score')->default(0);
            $table->unsignedTinyInteger('threshold')->default(60);
            $table->enum('status', ['aktif', 'selesai', 'kadaluarsa'])->default('aktif');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->unique(['user_id', 'kategori_id', 'learning_material_id', 'challenge_id', 'question_id'], 'recommendations_unique_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
