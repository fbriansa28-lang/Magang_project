<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_material_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_material_id')
                ->constrained('learning_materials')
                ->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->enum('file_type', ['image', 'video', 'document'])->default('document');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable()->comment('Ukuran file dalam bytes');
            $table->unsignedSmallInteger('order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_material_attachments');
    }
};