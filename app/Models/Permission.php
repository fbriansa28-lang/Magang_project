<?php

namespace App\Models;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Ambil data permission berdasarkan kata kunci pencarian.
     */
    public static function getPermissionByKeyword(string $keyword): LengthAwarePaginator
    {
        return static::query()
            ->when($keyword !== '', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Ambil data permission sesuai halaman (pagination).
     */
    public static function getPermissionByPage(int $page): LengthAwarePaginator
    {
        return static::query()
            ->orderBy('created_at', 'DESC')
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();
    }

    /**
     * Ambil detail satu permission berdasarkan id.
     */
    public static function getDetailPermission(int $permission_id): self
    {
        return static::findOrFail($permission_id);
    }

    /**
     * Simpan permission baru.
     */
    public static function insertPermission(array $data): self
    {
        return static::create([
            'name' => $data['name'],
        ]);
    }

    /**
     * Perbarui nama permission.
     */
    public static function updatePermission(int $permission_id, array $data): self
    {
        $permission = static::getDetailPermission($permission_id);
        $permission->update([
            'name' => $data['name'],
        ]);

        return $permission;
    }

    /**
     * Hapus permission.
     */
    public static function deletePermission(int $permission_id): bool
    {
        $permission = static::getDetailPermission($permission_id);

        return (bool) $permission->delete();
    }
}
