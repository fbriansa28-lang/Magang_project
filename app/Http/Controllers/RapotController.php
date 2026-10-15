<?php

namespace App\Http\Controllers;

use App\Models\CategoryScore;
use App\Models\Category;
use App\Models\Attempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RapotController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view rapot', only: ['index']),
            new Middleware('permission:view rapot|view rapot sendiri', only: ['show', 'challenges']),
        ];
    }

    public function index(Request $request): View
    {
        app(\App\Services\RecommendationService::class)->syncAllCategoryScores();

        $search     = trim((string) $request->input('search'));
        $categoryId = $request->integer('kategori') ?: null;
        $skorMin    = $request->input('skor_min');
        $skorMax    = $request->input('skor_max');
        $target     = $request->input('target', 'all');
        $year       = $request->integer('year') ?: null;
        $month      = $request->integer('month') ?: null;

        $categories = Category::active()->orderBy('name')->get();

        $participantsQuery = User::role('Peserta')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');

        $allParticipants = $participantsQuery->get()->map(function ($user) use ($categoryId, $year, $month) {
            return $this->buildParticipantRow($user, $categoryId, $year, $month);
        });

        if ($categoryId) {
            $allParticipants = $allParticipants->filter(fn ($row) => $row['has_category'])->values();
        }

        if ($target === 'lulus') {
            $allParticipants = $allParticipants->filter(fn ($row) => $row['status_kelulusan'] === 'LULUS')->values();
        } elseif ($target === 'tidak_lulus') {
            $allParticipants = $allParticipants->filter(fn ($row) => $row['status_kelulusan'] === 'TIDAK LULUS')->values();
        }

        if ($skorMin !== null && $skorMin !== '') {
            $allParticipants = $allParticipants->filter(fn ($row) => $row['skor_akhir'] >= (int) $skorMin)->values();
        }

        if ($skorMax !== null && $skorMax !== '' && (int) $skorMax > 0) {
            $allParticipants = $allParticipants->filter(fn ($row) => $row['skor_akhir'] <= (int) $skorMax)->values();
        }

        $perPage     = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $participants = new LengthAwarePaginator(
            $allParticipants->forPage($currentPage, $perPage)->values(),
            $allParticipants->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalPeserta        = User::role('Peserta')->count();
        $totalPesertaSelesai = User::role('Peserta')->whereHas('participantAttempts', fn ($q) => $q->finished())->count();
        
        $filteredRows = $allParticipants;
        $rataRataSkor = (int) round($filteredRows->avg(fn ($row) => (float) $row['skor_akhir']) ?? 0);
        $pesertaLulus = $filteredRows->where('status_kelulusan', 'LULUS')->count();
        $pesertaTidakLulus = $filteredRows->where('status_kelulusan', 'TIDAK LULUS')->count();

        return view('rapot.index', compact(
            'participants',
            'search',
            'categories',
            'categoryId',
            'skorMin',
            'skorMax',
            'target',
            'totalPeserta',
            'totalPesertaSelesai',
            'rataRataSkor',
            'pesertaLulus',
            'pesertaTidakLulus'
        ));
    }

    private function buildParticipantRow(User $user, ?int $categoryId, ?int $year = null, ?int $month = null): array
    {
        $scores = collect();
        $attempts = Attempt::with('challenge.category')
                ->where('user_id', $user->id)
                ->finished()
                ->when($year, fn ($q) => $q->whereYear('started_at', $year))
                ->when($month, fn ($q) => $q->whereMonth('started_at', $month))
                ->get();

            if ($attempts->isNotEmpty()) {
                $grouped = $attempts->groupBy(fn ($a) => $a->challenge?->kategori_id);
                $scores = $grouped->map(function ($catAttempts, $catId) {
                    $category = $catAttempts->first()->challenge?->category;
                    $totalScore = (float) $catAttempts->sum('total_score');
                    $attemptsCount = $catAttempts->count();
                    $avg = (int) round($totalScore / max(1, $attemptsCount));
                    $threshold = $category->point_ambang_batas ?? CategoryScore::DEFAULT_AMBANG_BATAS;
                    $isLemah = $avg < $threshold;

                    return (object) [
                        'kategori_id'    => $catId,
                        'category'       => $category,
                        'average_score'  => $avg,
                        'attempts_count' => $attemptsCount,
                        'total_score'    => (int) round($totalScore),
                        'is_lemah'       => $isLemah,
                        'isLemah'        => fn () => $isLemah,
                    ];
                });
            }

        $validScores = $scores->filter(fn ($score) => (int) ($score->attempts_count ?? 0) >= 3)->values();
        $hasCategory = $categoryId ? $validScores->contains('kategori_id', $categoryId) : $validScores->isNotEmpty();

        $selected = $categoryId
            ? $validScores->firstWhere('kategori_id', $categoryId)
            : $validScores->sortByDesc('average_score')->first();

        $skorAkhir = is_object($selected) ? (int) ($selected->average_score ?? 0) : 0;
        $kategoriUtama = is_object($selected) ? ($selected->category?->name ?? '-') : '-';

        $weakCount = $validScores->filter(fn ($score) => is_object($score) && ($score->is_lemah ?? false))->count();

        $statusKelulusan = ($selected && $weakCount === 0) ? 'LULUS' : 'TIDAK LULUS';

        return [
            'user'             => $user,
            'username'         => $user->username ?? $user->name,
            'skor_akhir'       => $skorAkhir,
            'kategori'         => $kategoriUtama,
            'status_kelulusan' => $statusKelulusan,
            'has_category'     => $hasCategory,
        ];
    }

    public function show(Request $request, User $user): View
    {
        $user = $this->resolveReportUser($user);

        $selectedCategoryId = $request->integer('category') ?: null;

        $scoresByCategory = CategoryScore::with('category')
            ->when($selectedCategoryId, fn ($query) => $query->where('kategori_id', $selectedCategoryId))
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('kategori_id');

        $completedChallengeIdsByCategory = Attempt::with('challenge:id,kategori_id')
            ->where('user_id', $user->id)
            ->finished()
            ->get()
            ->filter(fn ($a) => $a->challenge)
            ->groupBy(fn ($a) => $a->challenge->kategori_id)
            ->map(fn ($attempts) => $attempts->pluck('challenge_id')->unique()->count());

        // Ambil SELURUH kategori dulu (tidak dipaginate) — dipakai untuk
        // rata-rata progress dan label kategori. Jangan diganti dengan hasil
        // paginate di bawah, karena data harus mencerminkan seluruh kategori,
        // bukan cuma 1 halaman.
        $categoryScoresFull = Category::active()
            ->when($selectedCategoryId, fn ($query) => $query->whereKey($selectedCategoryId))
            ->withCount('activeChallenges')
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($scoresByCategory, $completedChallengeIdsByCategory) {
                $score = $scoresByCategory->get($category->id);

                $row = (object) [
                    'category'          => $category,
                    'kategori_id' => $category->id,
                    'total_challenges'  => $category->active_challenges_count,
                    'completed_count'   => $completedChallengeIdsByCategory->get($category->id, 0),
                    'attempts_count'    => $score->attempts_count ?? 0,
                    'total_score'       => $score->total_score ?? 0,
                    'average_score'     => $score->average_score ?? 0,
                ];

                $row->progress_percent = (int) round(min(3, $row->completed_count) / 3 * 100);

                $row->has_enough_attempts = true;

                $ambangBatas = $category->point_ambang_batas ?? CategoryScore::DEFAULT_AMBANG_BATAS;
                $row->is_lemah = $row->average_score < $ambangBatas;
                $row->status   = $row->is_lemah ? CategoryScore::STATUS_LEMAH : CategoryScore::STATUS_NORMAL;

                return $row;
            })
            ->sortByDesc('progress_percent')
            ->values();

        // Versi dipaginate, khusus untuk ditampilkan di card "Daftar Kategori"
        $perPage     = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage('page');
        $categoryScores = new LengthAwarePaginator(
            $categoryScoresFull->forPage($currentPage, $perPage)->values(),
            $categoryScoresFull->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('rapot.show', compact(
            'user',
            'categoryScores'
        ));
    }

    public function challenges(Request $request, User $user): View
    {
        $user = $this->resolveReportUser($user);

        $categoryId = $request->integer('category') ?: null;
        $category   = $categoryId ? Category::find($categoryId) : null;

        $attempts = Attempt::with('challenge.category')
            ->where('user_id', $user->id)
            ->when($categoryId, fn ($q) => $q->whereHas('challenge', fn ($qq) => $qq->where('kategori_id', $categoryId)))
            ->orderByDesc('finished_at')
            ->orderByDesc('started_at')
            ->get();

        $categoryLabel = $category->name ?? 'Seluruh Kategori';

        return view('rapot.challenges', compact('user', 'attempts', 'categoryLabel', 'category'));
    }

    /**
     * Peserta (hanya punya 'view rapot sendiri') selalu diarahkan ke rapot
     * miliknya sendiri; admin (punya 'view rapot') boleh membuka user mana pun.
     */
    private function resolveReportUser(User $user): User
    {
        if (auth()->user()?->can('view rapot')) {
            return $user;
        }

        abort_unless(auth()->id() === $user->id, 403);

        return $user;
    }
}