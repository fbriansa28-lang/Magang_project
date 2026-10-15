<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Leaderboard berbasis tabel `attempts` (nilai akhir skala 0-100).
 */
class LeaderboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view leaderboard', only: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $categoryId = $request->integer('category_id') ?: null;
        $year = $request->integer('year') ?: null;
        $month = $request->integer('month') ?: null;
        $search = trim((string) $request->input('search'));

        $challenges = Challenge::with('category')
            ->when($categoryId, fn ($query) => $query->where('kategori_id', $categoryId))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($search, fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('leaderboard.index', [
            'challenges' => $challenges,
            'totalChallenges' => Challenge::count(),
            'categoryOptions' => \App\Models\Category::orderBy('name')->get(['id', 'name']),
            'selectedCategory' => $categoryId,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'search' => $search,
        ]);
    }

    public function show(Request $request, Challenge $challenge): View
    {
        $challenge->load('category');

        // Guard akses: admin (punya 'show leaderboard') selalu boleh;
        // peserta hanya boleh bila sudah MENYELESAIKAN challenge ini.
        $canViewAll = auth()->user()?->can('show leaderboard') ?? false;
        $hasFinished = Attempt::where('challenge_id', $challenge->id)
            ->where('user_id', auth()->id())
            ->finished()
            ->exists();

        abort_unless($canViewAll || $hasFinished, 403);

        $allAttempts = Attempt::with('user:id,name,email,image')
            ->where('challenge_id', $challenge->id)
            ->finished()
            ->orderByDesc('total_score')
            ->orderBy('updated_at')
            ->get();

        $attemptUsers = $allAttempts->keyBy('user_id');
        $participants = \App\Models\User::role('Peserta')->get(['id', 'name', 'email', 'image'])->map(function ($user) use ($attemptUsers) {
            $attempt = $attemptUsers->get($user->id) ?? new Attempt(['user_id' => $user->id, 'total_score' => 0, 'status' => 'not_started']);
            return $attempt->setRelation('user', $user);
        });

        $maxScore = max(1.0, (float) $participants->max('total_score'));

        $ranked = $participants->sortByDesc('total_score')->values()->map(function (Attempt $attempt, $index) use ($maxScore) {
            $durationSeconds = null;
            if ($attempt->started_at && $attempt->finished_at) {
                $durationSeconds = $attempt->started_at->diffInSeconds($attempt->finished_at);
            }

            return (object) [
                'attempt_id' => $attempt->id,
                'rank' => $index + 1,
                'user_id' => $attempt->user_id,
                'user_name' => $attempt->user->name ?? 'Peserta',
                'user_email' => $attempt->user->email ?? '-',
                'user_image' => $attempt->user->image ?? null,
                'user_image_url' => $attempt->user?->image_url,
                'total_score' => (float) $attempt->total_score,
                'penalty_score' => round(100 - (float) $attempt->total_score, 2),
                'final_score' => (float) $attempt->total_score,
                'score_percent' => (int) round((float) $attempt->total_score / $maxScore * 100),
                'status' => $attempt->status,
                'started_at' => $attempt->started_at,
                'finished_at' => $attempt->finished_at,
                'duration_seconds' => $durationSeconds,
                'is_me' => $attempt->user_id === auth()->id(),
            ];
        });

        return view('leaderboard.show', [
            'challenge' => $challenge,
            'ranked' => $ranked,
        ]);
    }
}
