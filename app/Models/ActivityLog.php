<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'participant_activity_logs';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'attempt_id',
        'action',
        'description',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    const ACTION_START = 'start_challenge';
    const ACTION_SUBMIT_ANSWER = 'submit_answer';
    const ACTION_FINISH = 'finish_challenge';
    const ACTION_TIME_EXPIRED = 'time_expired';

    const ACTIONS = [
        self::ACTION_START => 'Memulai Challenge',
        self::ACTION_SUBMIT_ANSWER => 'Mengirim Jawaban',
        self::ACTION_FINISH => 'Menyelesaikan Challenge',
        self::ACTION_TIME_EXPIRED => 'Waktu Habis (Auto-selesai)',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Query Scopes — ActivityLog query logic
    |--------------------------------------------------------------------------
    | Setiap scope merepresentasikan satu langkah "ActivityLog -> DB" pada
    | sequence diagram (Buka Log Sistem, Cari Log, Filter Log, Ganti Halaman).
    | Controller tinggal memanggil getLog() sebagai entry point tunggal.
    */

    /** Batasi log hanya milik user tertentu, kecuali dia Admin. */
    public function scopeForViewer($query, bool $canViewAll, int $userId)
    {
        return $canViewAll ? $query : $query->where('user_id', $userId);
    }

    /** Sequence: "Cari Log" — cari berdasarkan kata kunci (nama/username/email user). */
    public function scopeSearchKeyword($query, ?string $keyword)
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->whereHas('user', function ($uq) use ($keyword) {
            $uq->where('name', 'like', "%{$keyword}%")
                ->orWhere('username', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%");
        });
    }

    /** Sequence: "Filter Log" — filter berdasarkan User (khusus Admin). */
    public function scopeFilterUser($query, $userId)
    {
        return $userId ? $query->where('user_id', $userId) : $query;
    }

    /** Sequence: "Filter Log" — filter berdasarkan Jenis Aksi. */
    public function scopeFilterAction($query, ?string $action)
    {
        return $action ? $query->where('action', $action) : $query;
    }

    /** Sequence: "Filter Log" — filter berdasarkan Rentang Waktu. */
    public function scopeFilterDateRange($query, ?string $dateFrom, ?string $dateTo)
    {
        return $query
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo));
    }

    /**
     * Entry point utama ActivityLog.
     *
     * Menyatukan 4 sequence (Buka Log Sistem / Cari Log / Filter Log / Ganti
     * Halaman) dalam satu query builder: saat Admin pindah halaman
     * (getLogByPage) atau menambah keyword/filter baru, paginator otomatis
     * mempertahankan filter yang sudah aktif lewat withQueryString().
     */
    public static function getLog(bool $canViewAll, int $userId, array $filters, int $perPage = 20)
    {
        return static::with(['user:id,name,username,email', 'challenge:id,title'])
            ->forViewer($canViewAll, $userId)
            ->when($canViewAll, fn ($q) => $q->searchKeyword($filters['search'] ?? null))
            ->when($canViewAll, fn ($q) => $q->filterUser($filters['user_id'] ?? null))
            ->filterAction($filters['action'] ?? null)
            ->filterDateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStatusKeyAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_TIME_EXPIRED => 'critical',
            self::ACTION_START => 'pending',
            default => 'success',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return strtoupper($this->status_key);
    }

    public static function record(Attempt $attempt, string $action, array $extra = []): self
    {
        return static::create([
            'user_id' => $attempt->user_id,
            'challenge_id' => $attempt->challenge_id,
            'attempt_id' => $attempt->id,
            'action' => $action,
            'description' => $extra['description'] ?? null,
            'meta' => $extra['meta'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}