<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedInteger('attempts_count')->default(0);
            $table->unsignedTinyInteger('average_score')->default(0);
            $table->enum('status', ['normal', 'lemah'])->default('normal');

            $table->timestamps();

            $table->unique(['user_id', 'kategori_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_scores');
    }
};
