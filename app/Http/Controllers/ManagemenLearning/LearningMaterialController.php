<?php

namespace App\Http\Controllers\ManagemenLearning;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Materi;
use App\Models\LearningMaterialAttachment;
use App\Traits\HandlesMediaUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LearningMaterialController extends Controller implements HasMiddleware
{
    use HandlesMediaUploads;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view kelola learning', only: ['index']),
            new Middleware('permission:create kelola learning', only: ['create', 'store']),
            new Middleware('permission:edit kelola learning', only: ['edit', 'update', 'destroyAttachment']),
            new Middleware('permission:delete kelola learning', only: ['destroy']),
        ];
    }

    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request): View
    {
        $materials = Materi::withCount(['questions', 'attachments'])
            ->with('category')
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->filled('category'), fn($q) => $q->where('kategori_id', $request->category))
            ->when($request->filled('year'), fn($q) => $q->whereYear('created_at', $request->year))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('created_at', $request->month))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();
        // Ambil tahun via PHP (portable lintas driver; YEAR() hanya MySQL).
        $years = Materi::query()
            ->pluck('created_at')
            ->map(fn ($createdAt) => $createdAt ? Carbon::parse($createdAt)->year : null)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();
        $totalMaterials = Materi::count();
        $newThisWeek = Materi::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        return view('managemenlearning.index', [
            'materials'      => $materials,
            'categories'     => $categories,
            'years'          => $years,
            'months'         => self::MONTHS,
            'totalMaterials' => $totalMaterials,
            'newThisWeek'    => $newThisWeek,
        ]);
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('managemenlearning.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMaterial($request);

        $material = Materi::create([
            'kategori_id' => $validated['kategori_id'],
            'level'       => $validated['level'],
            'title'       => $validated['title'],
            'description' => $validated['content'] ?? null,
            'content'     => $validated['content'] ?? null,
            'order'       => Materi::max('order') + 1,
            'is_active'   => $request->boolean('is_active', true),
            'created_by'  => auth()->id(),
        ]);

        $this->syncQuestions($material, $request);
        $this->storeAttachments($material, $request);

        return redirect()->route('learning-materials.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $learningMaterial): View
    {
        $learningMaterial->load('questions.optionRows', 'attachments');
        $categories = Category::active()->orderBy('name')->get();
        return view('managemenlearning.edit', ['material' => $learningMaterial, 'categories' => $categories]);
    }

    public function update(Request $request, Materi $learningMaterial): RedirectResponse
    {
        $validated = $this->validateMaterial($request);

        $data = [
            'kategori_id' => $validated['kategori_id'],
            'level'       => $validated['level'],
            'title'       => $validated['title'],
            'description' => $validated['content'] ?? null,
            'content'     => $validated['content'] ?? null,
            // Form edit tidak mengirim is_active: pertahankan nilai lama,
            // supaya materi tidak otomatis nonaktif (menyebabkan 404 di peserta).
            'is_active'   => $request->has('is_active')
                ? $request->boolean('is_active')
                : $learningMaterial->is_active,
        ];

        $learningMaterial->update($data);

        $this->syncQuestions($learningMaterial, $request);
        $this->storeAttachments($learningMaterial, $request);

        return redirect()->route('learning-materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroyAttachment(Materi $learningMaterial, LearningMaterialAttachment $attachment): RedirectResponse
    {
        abort_if($attachment->learning_material_id !== $learningMaterial->id, 404);

        $this->deleteMedia($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'File lampiran berhasil dihapus.');
    }

    public function destroy(Materi $learningMaterial): RedirectResponse
    {
        foreach ($learningMaterial->attachments as $attachment) {
            $this->deleteMedia($attachment->file_path);
        }

        $learningMaterial->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }

    private function validateMaterial(Request $request): array
    {
        return $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'level'       => 'required|in:easy,medium,hard',
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'content'     => 'nullable|string',
            'is_active'   => 'boolean',
            'attachments'   => 'nullable|array',
            'attachments.*' => [
                'file',
                'max:20480',
                'mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogv,ogg,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar',
            ],
            'questions.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);
    }

    private function storeAttachments(Materi $material, Request $request): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        $nextOrder = (int) $material->attachments()->max('order');

        foreach ($request->file('attachments') as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $nextOrder++;

            $material->attachments()->create([
                'file_path'     => $this->storeAttachment($file, 'learning/attachments'),
                'original_name' => $file->getClientOriginalName(),
                'file_type'     => LearningMaterialAttachment::resolveTypeFromMime($file->getMimeType()),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'order'         => $nextOrder,
                'uploaded_by'   => auth()->id(),
            ]);
        }
    }

    private function syncQuestions(Materi $material, Request $request): void
    {
        $questions = $request->input('questions', []);

        if (empty($questions)) {
            return;
        }

        $keepIds = [];

        foreach ($questions as $index => $q) {
            if (blank($q['question'] ?? null)) {
                continue;
            }

            $type = ($q['question_type'] ?? 'pilihan_ganda') === 'uraian' ? 'uraian' : 'pilihan_ganda';

            $data = [
                'question'       => $q['question'],
                'question_type'  => $type,
                'correct_option' => $type === 'pilihan_ganda' ? strtolower($q['correct_option'] ?? 'a') : null,
                'answer_key'     => $type === 'uraian' ? ($q['answer_key'] ?? null) : null,
                'hint'           => $q['hint'] ?? null,
                'order'          => $index + 1,
            ];

            $options = $type === 'pilihan_ganda' ? [
                'a' => $q['option_a'] ?? '',
                'b' => $q['option_b'] ?? '',
                'c' => $q['option_c'] ?? null,
                'd' => $q['option_d'] ?? null,
                'e' => $q['option_e'] ?? null,
            ] : [];

            $imageFile = $request->file("questions.$index.image");
            $existingQuestion = !empty($q['id']) ? $material->questions()->find($q['id']) : null;

            if ($imageFile) {
                if ($existingQuestion?->image_path) {
                    $this->deleteMedia($existingQuestion->image_path);
                }
                $data['image_path'] = $this->storeImage($imageFile, 'learning/questions');
            } elseif (($q['remove_image'] ?? '0') === '1') {
                if ($existingQuestion?->image_path) {
                    $this->deleteMedia($existingQuestion->image_path);
                }
                $data['image_path'] = null;
            }

            if (!empty($q['id'])) {
                $material->questions()->where('id', $q['id'])->update($data);
                $question = $material->questions()->find($q['id']);
                $keepIds[] = (int) $q['id'];
            } else {
                $question = $material->questions()->create($data);
                $keepIds[] = $question->id;
            }

            $question->syncOptions($options);
        }

        $removed = $material->questions()->whereNotIn('id', $keepIds)->get();
        foreach ($removed as $question) {
            $this->deleteMedia($question->image_path);
        }
        $material->questions()->whereNotIn('id', $keepIds)->delete();
    }
}