<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBank extends Model
{
    use HasFactory;

    protected $table = 'question_banks';

    protected $fillable = [
        'kategori_id',
        'group_id',
        'content',
        'image_path',
        'question_type',
        'flag',
        'correct_option',
        'difficulty',
        'hint',
        'explanation',
        'is_active',
        'created_by',
    ];

    protected $hidden = ['flag', 'correct_option'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];

    const TYPE_ESSAY = 'essay';
    const TYPE_PILIHAN_GANDA = 'pilihan_ganda';

    const DIFFICULTIES = [
        'easy'   => 'Rendah',
        'medium' => 'Medium',
        'hard'   => 'Tinggi',
    ];

    const MIN_STOCK_WARNING = 5;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function optionRows(): HasMany
    {
        return $this->hasMany(QuestionBankOption::class, 'question_bank_id')->orderBy('option_key');
    }

    /**
     * Snapshot soal ini di attempt peserta (fitur challenge acak berbobot).
     */
    public function attemptQuestions(): HasMany
    {
        return $this->hasMany(AttemptQuestion::class, 'question_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? \Storage::disk('public')->url($this->image_path) : null;
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

    public static function cekPenggunaan(int $soal_id): int
    {
        return self::findOrFail($soal_id)->attemptQuestions()->count();
    }

    public static function findById(int $soal_id): self
    {
        return self::findOrFail($soal_id);
    }

    public static function simpan(array $data): self
    {
        return self::create($data);
    }

    public static function updateSoal(int $soal_id, array $data): self
    {
        $soal = self::findById($soal_id);
        $soal->update($data);

        return $soal;
    }

    public static function hapusSoal(int $soal_id): bool
    {
        return (bool) self::findById($soal_id)->delete();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function cekDuplikat(string $content, ?int $ignoreId = null)
    {
        return self::query()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->whereRaw('LOWER(TRIM(content)) = ?', [mb_strtolower(trim($content))])
            ->first();
    }

    public static function hitungJumlahPerDifficulty(): array
    {
        $counts = self::active()
            ->selectRaw('difficulty, COUNT(*) as total')
            ->groupBy('difficulty')
            ->pluck('total', 'difficulty');

        $recentCounts = self::active()
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('difficulty, COUNT(*) as total')
            ->groupBy('difficulty')
            ->pluck('total', 'difficulty');

        $stats = [];
        foreach (self::DIFFICULTIES as $key => $label) {
            $stats[$key] = [
                'label'  => $label,
                'total'  => (int) ($counts[$key] ?? 0),
                'recent' => (int) ($recentCounts[$key] ?? 0),
            ];
        }

        return $stats;
    }

    public static function totalStatistics(): array
    {
        return [
            'label'  => 'soal',
            'total'  => self::active()->count(),
            'recent' => self::active()->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Statistik stok soal untuk kartu di form kelola challenge:
     * total & per tingkat + jumlah soal baru (7 hari terakhir).
     * Dibatasi kategori bila $categoryId diisi (null = seluruh kategori),
     * meniru kartu halaman Bank Soal namun mengikuti kategori terpilih.
     *
     * @return array<string, array{total: int, recent: int}>
     */
    public static function statisticsForCategory(?int $categoryId = null): array
    {
        $base = fn () => self::active()->when($categoryId, fn ($q) => $q->where('kategori_id', $categoryId));

        $counts = $base()
            ->selectRaw('difficulty, COUNT(*) as total')
            ->groupBy('difficulty')
            ->pluck('total', 'difficulty');

        $recentCounts = $base()
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('difficulty, COUNT(*) as total')
            ->groupBy('difficulty')
            ->pluck('total', 'difficulty');

        $stats = [
            'total' => [
                'total'  => $base()->count(),
                'recent' => $base()->where('created_at', '>=', now()->subDays(7))->count(),
            ],
        ];

        foreach (self::DIFFICULTIES as $key => $label) {
            $stats[$key] = [
                'total'  => (int) ($counts[$key] ?? 0),
                'recent' => (int) ($recentCounts[$key] ?? 0),
            ];
        }

        return $stats;
    }
}
