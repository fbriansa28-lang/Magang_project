<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Materi;
use App\Models\LearningProgress;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class MateriController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view learning', only: ['index']),
            new Middleware('permission:show learning', only: ['show']),
        ];
    }

    public function index(Request $request, RecommendationService $recommendationService): View
    {
        $userId = auth()->id();

        // Rekomendasi aktif diambil lebih dulu; materi yang sudah tampil di
        // rekomendasi dikeluarkan dari daftar Materi Belajar agar tidak double.
        $recommendations = $recommendationService->activeFor($userId);

        // Progress user untuk seluruh materi rekomendasi (dipakai untuk sorting
        // dan status card; menghindari query berulang di view).
        $recommendedMaterialIdsForProgress = $recommendations->pluck('learning_material_id')->filter()->unique()->values();
        $progressByMaterial = LearningProgress::where('user_id', $userId)
            ->whereIn('learning_material_id', $recommendedMaterialIdsForProgress)
            ->get()
            ->keyBy('learning_material_id');

        // Rekomendasi yang SUDAH dikerjakan dipindah ke belakang, sehingga yang
        // belum dikerjakan selalu tampil di depan.
        $recommendations = $recommendations
            ->sortBy(function ($recommendation) use ($progressByMaterial) {
                $materialId = $recommendation->learning_material_id;

                if (! $materialId) {
                    return 0;
                }

                return ($progressByMaterial->get($materialId)?->status === 'completed') ? 1 : 0;
            })
            ->values();

        $recommendedMaterialIds = $recommendations->pluck('learning_material_id')->filter()->unique()->values();

        $materials = Materi::where('is_active', true)
            ->whereNotIn('id', $recommendedMaterialIds)
            ->with('category')
            ->withCount(['questions', 'attachments'])
            ->with(['attachments' => fn ($query) => $query->orderBy('order')])
            ->with(['progresses' => fn ($query) => $query->where('user_id', $userId)])
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->filled('level'), fn ($query) => $query->where('level', $request->level))
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori_id', $request->kategori))
            ->when($request->filled('tahun'), fn ($query) => $query->whereYear('created_at', $request->tahun))
            ->when($request->filled('bulan'), fn ($query) => $query->whereMonth('created_at', $request->bulan))
            ->orderBy('order')
            ->get()
            ->map(function ($material) {
                $progress = $material->progresses->first();
                $material->my_status = $progress->status ?? 'not_started';
                $material->my_correct_count = $progress->correct_count ?? null;
                $material->my_total_questions = $progress->total_questions ?? null;
                $material->is_terbaru = $material->created_at?->greaterThan(now()->subDays(14)) ?? false;
                return $material;
            })
            ->sortBy(fn ($material) => $material->my_status === 'completed' ? 1 : 0)
            ->values();

        $categories = Category::active()->orderBy('name')->get();
        // Ambil tahun via PHP (portable lintas driver; YEAR() hanya MySQL).
        $years = Materi::query()
            ->pluck('created_at')
            ->map(fn ($createdAt) => $createdAt ? \Carbon\Carbon::parse($createdAt)->year : null)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('learning.index', compact('materials', 'categories', 'years', 'recommendations', 'progressByMaterial'));
    }

    public function show(Materi $learningMaterial): View
    {
        abort_unless($learningMaterial->is_active, 404);

        $learningMaterial->loadCount('questions');
        $learningMaterial->load('attachments');
        $progress = LearningProgress::where('user_id', auth()->id())
            ->where('learning_material_id', $learningMaterial->id)
            ->first();

        return view('learning.show', ['material' => $learningMaterial, 'progress' => $progress]);
    }
}
