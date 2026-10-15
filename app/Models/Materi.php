<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materi extends Model
{
    protected $table = 'learning_materials';

    protected $fillable = [
        'kategori_id',
        'level',
        'title',
        'description',
        'content',
        'order',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    const LEVELS = [
        'easy'   => 'Mudah',
        'medium' => 'Sedang',
        'hard'   => 'Sulit',
    ];

    public function getLevelLabelAttribute(): string
    {
        return self::LEVELS[$this->level] ?? $this->level;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Simulasi::class, 'learning_material_id')->orderBy('order');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LearningMaterialAttachment::class, 'learning_material_id')->orderBy('order')->orderBy('id');
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(LearningProgress::class, 'learning_material_id');
    }
}
