<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'name',
        'weight_score',
        'point_ambang_batas',
        'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'weight_score'       => 'integer',
        'point_ambang_batas' => 'integer',
    ];

    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'kategori_id');
    }

    public function activeChallenges()
    {
        return $this->hasMany(Challenge::class, 'kategori_id')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getAllKategori(int $perPage = 15)
    {
        return self::withCount('challenges')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public static function cekKategoriExist(string $namaKategori, ?int $excludeId = null): bool
    {
        return self::query()
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($namaKategori))])
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->exists();
    }

    public static function simpanKategori(string $namaKategori, int $bobotSkor): self
    {
        return self::create([
            'name'               => $namaKategori,
            'weight_score'       => $bobotSkor,
            'point_ambang_batas' => $bobotSkor,
        ]);
    }

    public static function updateKategori(int $kategori_id, int $bobot_skor_baru): bool
    {
        $kategori = self::findOrFail($kategori_id);

        return $kategori->update([
            'weight_score'       => $bobot_skor_baru,
            'point_ambang_batas' => $bobot_skor_baru,
        ]);
    }

    public static function cekKategoriDipakai(int $kategori_id): bool
    {
        return self::findOrFail($kategori_id)->challenges()->exists();
    }

    public static function deleteKategori(int $kategori_id): bool
    {
        return (bool) self::findOrFail($kategori_id)->delete();
    }
}
