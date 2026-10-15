<?php

namespace App\Http\Controllers;

use App\Models\AttemptQuestion;
use App\Services\AttemptService;
use App\Services\ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use RuntimeException;

class AttemptQuestionController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly AttemptService $attemptService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:start challenge', only: ['openHint']),
        ];
    }

    public function openHint(AttemptQuestion $attemptQuestion): JsonResponse
    {
        $attemptQuestion->loadMissing(['attempt', 'question']);
        abort_unless(
            $attemptQuestion->attempt?->user_id === auth()->id()
            && $attemptQuestion->attempt?->isInProgress() === true
            && filled($attemptQuestion->question?->hint),
            403
        );

        try {
            $hint = $this->attemptService->openHint($attemptQuestion);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }

        $attemptQuestion->refresh();

        return response()->json([
            'success' => true,
            'hint' => $hint,
            'hint_penalty_percent' => round(ScoringService::HINT_PENALTY * 100, 2),
            'hint_opened' => $attemptQuestion->hintUsed(),
        ]);
    }
}
