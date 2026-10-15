<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Simulasi extends Model
{
    protected $table = 'learning_questions';

    protected $fillable = [
        'learning_material_id',
        'question',
        'image_path',
        'question_type',
        'correct_option',
        'answer_key',
        'hint',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    protected $appends = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];

    const TYPE_PILIHAN_GANDA = 'pilihan_ganda';
    const TYPE_URAIAN = 'uraian';

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? \Storage::disk('public')->url($this->image_path) : null;
    }

    public function isPilihanGanda(): bool
    {
        return $this->question_type === self::TYPE_PILIHAN_GANDA;
    }

    public function isUraian(): bool
    {
        return $this->question_type === self::TYPE_URAIAN;
    }

    public const URAIAN_SIMILARITY_THRESHOLD = 75.0;

    public function isCorrect(string $answer): bool
    {
        if ($this->isUraian()) {
            if (blank($this->answer_key)) {
                return filled(trim($answer));
            }
            return self::textSimilarity($answer, $this->answer_key) >= self::URAIAN_SIMILARITY_THRESHOLD;
        }

        return strtolower(trim($answer)) === $this->correct_option;
    }

    public static function textSimilarity(string $a, string $b): float
    {
        $a = strtolower(trim($a));
        $b = strtolower(trim($b));

        if ($a === '' || $b === '') {
            return 0.0;
        }

        similar_text($a, $b, $percent);

        return round($percent, 2);
    }

    public function optionRows(): HasMany
    {
        return $this->hasMany(LearningQuestionOption::class, 'learning_question_id')->orderBy('option_key');
    }

    public function options(): array
    {
        return $this->optionRows->pluck('option_text', 'option_key')->all();
    }

    protected function optionValue(string $key): ?string
    {
        return $this->optionRows->firstWhere('option_key', $key)?->option_text;
    }

    public function getOptionAAttribute(): ?string { return $this->optionValue('a'); }
    public function getOptionBAttribute(): ?string { return $this->optionValue('b'); }
    public function getOptionCAttribute(): ?string { return $this->optionValue('c'); }
    public function getOptionDAttribute(): ?string { return $this->optionValue('d'); }
    public function getOptionEAttribute(): ?string { return $this->optionValue('e'); }

    public function syncOptions(array $options): void
    {
        foreach (['a', 'b', 'c', 'd', 'e'] as $key) {
            $value = $options[$key] ?? null;

            if (filled($value)) {
                $this->optionRows()->updateOrCreate(
                    ['option_key' => $key],
                    ['option_text' => $value]
                );
            } else {
                $this->optionRows()->where('option_key', $key)->delete();
            }
        }

        $this->unsetRelation('optionRows');
    }
}
