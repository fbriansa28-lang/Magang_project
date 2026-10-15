<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Category;
use App\Models\Materi;
use App\Models\LearningProgress;
use App\Models\Attempt;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        if (auth()->user()->hasRole('Admin')) {
            return $this->admin();
        }

        return $this->peserta();
    }

    protected function admin(): View
    {
        $totalUser = User::role('Peserta')->count();
        $totalChallenge = Challenge::count();
        $totalLearning = Materi::where('is_active', true)->count();
        $categoryId = request()->integer('category_id') ?: null;
        $challengeId = request()->integer('challenge_id') ?: null;
        $categoryOptions = Category::active()->orderBy('name')->get(['id', 'name']);
        $challengeOptions = Challenge::orderBy('title')->get(['id', 'title']);
        $period = request()->input('period', 'actual');
        $moduleActivity = Category::withCount([
                'challenges as attempts_count' => function ($query) use ($period) {
                    $query->join('attempts', 'attempts.challenge_id', '=', 'challenges.id')
                        ->where('attempts.status', 'completed')
                        ->when($period === '7_days', fn ($attempts) => $attempts->where('attempts.finished_at', '>=', now()->subDays(7)));
                },
            ])
            ->orderByDesc('attempts_count')
            ->get()
            ->map(fn ($category) => [
                'name' => strtoupper($category->name),
                'count' => $category->attempts_count,
            ]);

        $maxActivity = max(1, $moduleActivity->max('count') ?? 1);

        $top5 = Attempt::query()
            ->with('user:id,name,username,image', 'challenge:id,kategori_id')
            ->finished()
            ->whereNotNull('finished_at')
            ->when($challengeId, fn ($query) => $query->where('challenge_id', $challengeId))
            ->when($categoryId, fn ($query) => $query->whereHas('challenge', fn ($challenge) => $challenge->where('kategori_id', $categoryId)))
            ->get()
            ->groupBy('user_id')
            ->map(function ($attempts) {
                // total_score sudah skala 0-100, langsung dipakai sebagai persentase.
                $percentages = $attempts->map(fn ($attempt) => min(100, (float) $attempt->total_score));

                return (object) [
                    'user' => $attempts->first()->user,
                    'average_score' => round($percentages->avg() ?? 0, 2),
                    'avg_duration' => $attempts->avg(fn ($attempt) => $attempt->started_at && $attempt->finished_at
                        ? $attempt->started_at->diffInSeconds($attempt->finished_at) / 60
                        : null),
                    'total_challenge' => $attempts->count(),
                ];
            })
            ->sortByDesc('average_score')
            ->take(5)
            ->values()
            ->map(function ($row, $index) {
                $row->rank = $index + 1;
                return $row;
            });

        return view('dashboard.admin', compact('totalUser', 'totalChallenge', 'totalLearning', 'moduleActivity', 'maxActivity', 'top5', 'categoryOptions', 'challengeOptions', 'categoryId', 'challengeId', 'period'));
    }

    protected function peserta(): View
    {
        $userId = auth()->id();

        $completedAttempts = Attempt::where('user_id', $userId)
            ->finished()
            ->get();

        $completedChallengeIds = $completedAttempts->pluck('challenge_id')->toArray();

        $activeChallenges = Challenge::activeByTime()
            ->whereNotIn('id', $completedChallengeIds)
            ->with('category')
            ->withCount('challengeQuestions')
            ->latest()
            ->take(4)
            ->get();

        $totalLearning = Materi::where('is_active', true)->count();
        $completedLearning = LearningProgress::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();
        $notCompletedLearning = max(0, $totalLearning - $completedLearning);

        $latestLearning = Materi::where('is_active', true)
            ->withCount('questions')
            ->with(['progresses' => fn ($q) => $q->where('user_id', $userId)])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($material) {
                $material->my_status = $material->progresses->first()->status ?? 'not_started';
                return $material;
            });

        return view('dashboard.peserta', compact(
            'activeChallenges',
            'totalLearning',
            'completedLearning',
            'notCompletedLearning',
            'latestLearning'
        ));
    }
}