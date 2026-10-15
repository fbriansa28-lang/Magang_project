<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'image'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function participantAttempts()
    {
        return $this->hasMany(Attempt::class);
    }

    /**
     * Ambil detail satu user berdasarkan id.
     */
    public static function getDetailUser(int $user_id): self
    {
        return static::findOrFail($user_id);
    }

    /**
     * Perbarui data user (role).
     */
    public static function updateUser(int $user_id, array $data): self
    {
        $user = static::getDetailUser($user_id);
        $user->syncRoles($data['role']);

        return $user;
    }

    /**
     * Hapus user. Mengembalikan false bila user tidak ditemukan.
     */
    public static function deleteUser(int $user_id): bool
    {
        $user = static::find($user_id);

        if (!$user) {
            return false;
        }

        return (bool) $user->delete();
    }
}
