<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterialAttachment extends Model
{
    protected $fillable = [
        'learning_material_id',
        'file_path',
        'original_name',
        'file_type',
        'mime_type',
        'size',
        'order',
        'uploaded_by',
    ];

    protected $casts = [
        'size'  => 'integer',
        'order' => 'integer',
    ];

    const TYPE_IMAGE    = 'image';
    const TYPE_VIDEO    = 'video';
    const TYPE_DOCUMENT = 'document';

    public function getUrlAttribute(): string
    {
        return \Storage::disk('public')->url($this->file_path);
    }

    public static function resolveTypeFromMime(?string $mime): string
    {
        if (!$mime) {
            return self::TYPE_DOCUMENT;
        }

        if (str_starts_with($mime, 'image/')) {
            return self::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return self::TYPE_VIDEO;
        }

        return self::TYPE_DOCUMENT;
    }
}