<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participant_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attempt_id')->nullable()
                ->constrained('attempts')->nullOnDelete();

            $table->string('action');

            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['challenge_id', 'user_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participant_activity_logs');
    }
};