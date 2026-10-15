<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\Category;
use App\Models\CategoryScore;
use App\Models\Challenge;
use App\Models\Materi;
use App\Models\QuestionBank;
use App\Models\Recommendation;
use Illuminate\Support\Collection;

class RecommendationService
{
    public function syncAllCategoryScores(): void
    {
        $finishedAttempts = Attempt::with('challenge:id,kategori_id')
            ->finished()
            ->get()
            ->filter(fn ($a) => $a->challenge && $a->challenge->kategori_id);

        $grouped = $finishedAttempts->groupBy(fn ($a) => $a->user_id . '_' . $a->challenge->kategori_id);

        foreach ($grouped as $key => $attempts) {
            $first = $attempts->first();
            $userId = (int) $first->user_id;
            $categoryId = (int) $first->challenge->kategori_id;
            $this->refreshCategory($userId, $categoryId);
        }
    }

    public function refreshForChallenge(int $userId, int $challengeId): void
    {
        $challenge = Challenge::find($challengeId);
        if (!$challenge?->kategori_id) return;

        $this->refreshCategory($userId, (int) $challenge->kategori_id);
    }

    public function refreshCategory(int $userId, int $categoryId): void
    {
        // total_score attempt sudah skala 0-100, jadi langsung dipakai.
        $scores = Attempt::query()
            ->where('user_id', $userId)
            ->finished()
            ->whereHas('challenge', fn ($query) => $query->where('kategori_id', $categoryId))
            ->get()
            ->map(fn ($attempt) => min(100, (float) $attempt->total_score));

        if ($scores->isEmpty()) return;

        $average = (int) round($scores->avg());
        $threshold = Category::find($categoryId)?->point_ambang_batas ?? CategoryScore::DEFAULT_AMBANG_BATAS;
        $status = $average < $threshold ? CategoryScore::STATUS_LEMAH : CategoryScore::STATUS_NORMAL;

        CategoryScore::updateOrCreate(
            ['user_id' => $userId, 'kategori_id' => $categoryId],
            ['total_score' => (int) round($scores->sum()), 'attempts_count' => $scores->count(), 'average_score' => $average, 'status' => $status]
        );

        if ($status === CategoryScore::STATUS_NORMAL) {
            Recommendation::where('user_id', $userId)->where('kategori_id', $categoryId)->where('status', 'aktif')->update(['status' => 'selesai']);
            return;
        }

        $this->storeRecommendations($userId, $categoryId, $average, $threshold);
    }

    private function storeRecommendations(int $userId, int $categoryId, int $average, int $threshold): void
    {
        foreach (Materi::where('kategori_id', $categoryId)->where('is_active', true)->get() as $material) {
            Recommendation::updateOrCreate(
                ['user_id' => $userId, 'kategori_id' => $categoryId, 'learning_material_id' => $material->id, 'challenge_id' => null, 'question_id' => null],
                ['average_score' => $average, 'threshold' => $threshold, 'status' => 'aktif']
            );
        }

        // Soal bank di kategori ini yang belum pernah muncul di attempt user.
        // Rekomendasi soal butuh target challenge untuk tombol "kerjakan":
        // pakai challenge aktif pertama yang punya set soal di kategori ini.
        $targetChallenge = Challenge::query()
            ->where('kategori_id', $categoryId)
            ->where('is_active', true)
            ->whereHas('challengeQuestions')
            ->orderBy('id')
            ->first();

        if (! $targetChallenge) {
            return;
        }

        $triedQuestionIds = AttemptQuestion::query()
            ->whereHas('attempt', fn ($query) => $query->where('user_id', $userId))
            ->pluck('question_id');

        $questions = QuestionBank::where('is_active', true)
            ->where('kategori_id', $categoryId)
            ->whereNotIn('id', $triedQuestionIds)
            ->limit(10)
            ->get();

        foreach ($questions as $question) {
            Recommendation::updateOrCreate(
                ['user_id' => $userId, 'kategori_id' => $categoryId, 'learning_material_id' => null, 'challenge_id' => $targetChallenge->id, 'question_id' => $question->id],
                ['average_score' => $average, 'threshold' => $threshold, 'status' => 'aktif']
            );
        }
    }

    public function activeFor(int $userId): Collection
    {
        return Recommendation::with(['category', 'material', 'challenge'])
            ->where('user_id', $userId)->where('status', 'aktif')->latest()->get();
    }
}
