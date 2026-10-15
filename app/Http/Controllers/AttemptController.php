<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\Challenge;
use App\Services\AttemptService;
use App\Services\SimilarityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use RuntimeException;

class AttemptController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly AttemptService $attemptService,
        private readonly SimilarityService $similarityService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:start challenge', only: ['start', 'show', 'submitAnswer', 'finish']),
        ];
    }

    public function start(Challenge $challenge): RedirectResponse
    {
        if (! $challenge->isActiveNow()) {
            abort(403, 'Challenge tidak aktif saat ini.');
        }

        try {
            $attempt = $this->attemptService->start($challenge, auth()->user());
        } catch (RuntimeException $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }

        if (! $attempt->isInProgress()) {
            return redirect()->route('attempts.result', $attempt);
        }

        return redirect()->route('attempts.work', $attempt);
    }

    public function show(Attempt $attempt): View|RedirectResponse
    {
        $this->authorizeView($attempt);

        if (! $attempt->isInProgress()) {
            return redirect()->route('attempts.result', $attempt);
        }

        $attempt->load(['challenge.category']);
        $questions = $this->attemptService->questionsPayload($attempt);
        $remainingSeconds = $this->attemptService->remainingSeconds($attempt);

        // Ringkasan pengerjaan untuk sidebar (kartu STATUS, REWARD, dll).
        $attemptQuestions = $attempt->attemptQuestions;
        $finalizedCount = $attemptQuestions->filter(fn (AttemptQuestion $q) => $q->isFinalized())->count();
        $totalQuestions = $attemptQuestions->count();
        $xp = (float) $attemptQuestions->sum(fn (AttemptQuestion $q) => (float) ($q->score ?? 0));
        $progress = (float) ($attempt->total_score ?? 0);

        // Peringkat sementara di challenge ini: attempt selesai dengan skor
        // lebih tinggi (atau skor sama tapi selesai lebih dulu) + 1.
        $rank = Attempt::where('challenge_id', $attempt->challenge_id)
            ->finished()
            ->where(function ($q) use ($attempt) {
                $q->where('total_score', '>', $attempt->total_score)
                    ->orWhere(function ($q2) use ($attempt) {
                        $q2->where('total_score', $attempt->total_score)
                            ->where('updated_at', '<', $attempt->updated_at);
                    });
            })
            ->count() + 1;

        $totalParticipants = Attempt::where('challenge_id', $attempt->challenge_id)->count();

        return view('challenge.attempt', [
            'attempt' => $attempt,
            'questions' => $questions,
            'remainingSeconds' => $remainingSeconds,
            'finalizedCount' => $finalizedCount,
            'totalQuestions' => $totalQuestions,
            'xp' => $xp,
            'progress' => $progress,
            'rank' => $rank,
            'totalParticipants' => $totalParticipants,
        ]);
    }

    public function submitAnswer(Request $request, Attempt $attempt): JsonResponse
    {
        $this->authorizeAnswer($attempt);

        $validated = $request->validate([
            'attempt_question_id' => ['required', 'integer'],
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $attemptQuestion = $attempt->attemptQuestions()
            ->whereKey($validated['attempt_question_id'])
            ->firstOrFail();

        try {
            $result = $this->attemptService->submitAnswer($attemptQuestion, $validated['answer']);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true] + $result);
    }

    public function finish(Request $request, Attempt $attempt): RedirectResponse
    {
        $this->authorizeView($attempt);

        $timeExpired = $request->input('reason') === 'time_expired';

        $attempt = $this->attemptService->finish($attempt, $timeExpired);

        return redirect()->route('attempts.result', $attempt)
            ->with('success', $timeExpired
                ? 'Waktu habis — jawaban tersimpan dinilai otomatis.'
                : 'Challenge selesai.');
    }

    public function result(Attempt $attempt): View
    {
        $this->authorizeView($attempt);

        $attempt->load(['challenge.category', 'attemptQuestions.question']);

        $questions = $attempt->attemptQuestions;
        $totalWeight = (float) $questions->sum(fn ($question) => (float) $question->weight);
        $totalRaw = (float) $questions->sum(fn ($question) => (float) ($question->raw_score ?? 0));
        $totalScore = (float) $questions->sum(fn ($question) => (float) ($question->score ?? 0));

        // Statistik ringkasan untuk kartu laporan hasil.
        $correctCount = $questions->where('status', AttemptQuestion::STATUS_CORRECT)->count();
        $wrongCount = $questions->where('status', AttemptQuestion::STATUS_WRONG)->count();
        $unansweredCount = $questions->where('status', AttemptQuestion::STATUS_IN_PROGRESS)->count();
        $hintCount = $questions->filter(fn (AttemptQuestion $q) => $q->hintUsed())->count();

        $durationSeconds = null;
        if ($attempt->started_at && $attempt->finished_at) {
            $durationSeconds = $attempt->started_at->diffInSeconds($attempt->finished_at);
        }

        // Peringkat peserta di challenge ini (attempt selesai bernilai lebih tinggi + 1).
        $rank = Attempt::where('challenge_id', $attempt->challenge_id)
            ->finished()
            ->where(function ($q) use ($attempt) {
                $q->where('total_score', '>', $attempt->total_score)
                    ->orWhere(function ($q2) use ($attempt) {
                        $q2->where('total_score', $attempt->total_score)
                            ->where('updated_at', '<', $attempt->updated_at);
                    });
            })
            ->count() + 1;

        $totalParticipants = Attempt::where('challenge_id', $attempt->challenge_id)->count();

        // Similarity uraian dihitung live (tidak disimpan di DB) untuk laporan hasil.
        $similarities = [];
        foreach ($questions as $question) {
            if (($question->type === 'essay' || $question->type === 'uraian') && filled($question->answer)) {
                $similarities[$question->id] = $this->similarityService
                    ->hitungSimilarity((string) $question->answer, (string) ($question->question?->flag ?? ''));
            }
        }

        return view('challenge.result', compact(
            'attempt',
            'questions',
            'totalWeight',
            'totalRaw',
            'totalScore',
            'correctCount',
            'wrongCount',
            'unansweredCount',
            'hintCount',
            'durationSeconds',
            'rank',
            'totalParticipants',
            'similarities'
        ));
    }

    private function authorizeView(Attempt $attempt): void
    {
        $user = auth()->user();
        abort_unless(
            $user && ($user->id === $attempt->user_id || $user->hasRole('Admin') || $user->can('show leaderboard') || $user->can('view leaderboard')),
            403
        );
    }

    private function authorizeAnswer(Attempt $attempt): void
    {
        $user = auth()->user();
        abort_unless(
            $user && $user->id === $attempt->user_id && $attempt->isInProgress(),
            403
        );
    }
}
