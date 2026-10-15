<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->nullable()
                ->constrained('kategori')
                ->nullOnDelete();
            $table->enum('level', ['easy', 'medium', 'hard'])->default('easy');
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable()->comment('Isi materi/bacaan');
            $table->unsignedSmallInteger('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};
