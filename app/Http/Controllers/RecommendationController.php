<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RecommendationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:view recommendation', only: ['openMaterial', 'selectQuestion'])];
    }

    public function openMaterial(Recommendation $recommendation)
    {
        abort_unless($recommendation->user_id === auth()->id() && $recommendation->status === 'aktif', 403);
        abort_unless($recommendation->learning_material_id, 404);
        return redirect()->route('learning.show', $recommendation->learning_material_id);
    }

    public function selectQuestion(Recommendation $recommendation)
    {
        abort_unless($recommendation->user_id === auth()->id() && $recommendation->status === 'aktif', 403);
        abort_unless($recommendation->challenge_id, 404);
        return redirect()->route('attempts.start', $recommendation->challenge_id);
    }
}
