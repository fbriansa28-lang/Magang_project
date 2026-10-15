<?php

namespace App\Http\Controllers\ManagemenChallenge;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Category;
use App\Models\QuestionBank;
use App\Services\ChallengeQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Kelola challenge (set soal tetap):
 * - Admin menentukan jumlah soal per tingkat (mudah/sedang/sulit).
 * - Sistem memecah otomatis PG/Uraian, memilih soal acak SEKALI saat save,
 *   dan menyimpannya sebagai set tetap untuk semua peserta.
 * - Simpan DIBLOKIR bila stok bank soal satu tingkat kurang dari jumlah
 *   yang diminta (alert berisi stok tersedia).
 */
class ChallengeController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly ChallengeQuestionService $challengeQuestionService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view challenge', only: ['index']),
            new Middleware('permission:create challenge', only: ['create', 'store']),
            new Middleware('permission:edit challenge', only: ['edit', 'update']),
            new Middleware('permission:delete challenge', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Challenge::with('category')
            ->withCount('challengeQuestions')
            ->withSum('challengeQuestions as set_weight_sum', 'weight');

        if ($request->filled('category')) $query->where('kategori_id', $request->category);
        if ($request->filled('year'))     $query->whereYear('created_at', $request->year);
        if ($request->filled('month'))    $query->whereMonth('created_at', $request->month);
        if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');

        $challenges = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        // Ambil tahun via PHP (portable lintas driver; YEAR() hanya MySQL).
        $years = Challenge::query()
            ->pluck('created_at')
            ->map(fn ($createdAt) => $createdAt ? \Carbon\Carbon::parse($createdAt)->year : null)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();
        $totalChallenges = Challenge::count();
        $activeChallenges = Challenge::where('is_active', true)->count();
        $inactiveChallenges = Challenge::where('is_active', false)->count();
        $newThisWeek = Challenge::where('created_at', '>=', now()->startOfWeek())->count();

        return view('managemenchallenge.challenge.index', [
            'challenges' => $challenges,
            'categories' => $categories,
            'years' => $years,
            'totalChallenges' => $totalChallenges,
            'activeChallenges' => $activeChallenges,
            'inactiveChallenges' => $inactiveChallenges,
            'newThisWeek' => $newThisWeek,
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ],
        ]);
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();

        // Kartu stok menampilkan total SELURUH kategori sampai admin memilih
        // kategori; JS lalu menggantinya ke stok kategori terpilih.
        $stockByCategory = $this->stockByCategory();
        $selectedCategoryId = (int) old('kategori_id', 0);
        $stockStats = QuestionBank::statisticsForCategory($selectedCategoryId ?: null);

        return view('managemenchallenge.challenge.create', compact(
            'categories',
            'stockByCategory',
            'stockStats',
            'selectedCategoryId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateChallenge($request);

        $counts = [
            'easy' => (int) $validated['questions_easy'],
            'medium' => (int) $validated['questions_medium'],
            'hard' => (int) $validated['questions_hard'],
        ];

        $this->assertStockEnough((int) $validated['kategori_id'], $counts);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();
        $validated['starts_at']  = $this->parseLocalDateTime($validated['starts_at'] ?? null);
        $validated['ends_at']    = $this->parseLocalDateTime($validated['ends_at'] ?? null);

        $challenge = DB::transaction(function () use ($validated, $counts) {
            $challenge = Challenge::create($validated);
            $this->challengeQuestionService->buildSet($challenge, $counts);

            return $challenge;
        });

        $summary = $this->challengeQuestionService->setSummary($challenge);

        return redirect()->route('challenges.index')
            ->with('success', sprintf(
                'Challenge berhasil dibuat dengan %d soal (total bobot %s = 100%%).',
                $summary['total_questions'],
                number_format($summary['total_weight'], 2)
            ));
    }

    public function edit(Challenge $challenge): View
    {
        $challenge->load(['category', 'challengeQuestions.question']);
        $categories = Category::active()->orderBy('name')->get();

        // Kartu stok mengikuti kategori challenge ini (atau kategori terpilih
        // saat validasi gagal mengembalikan old input).
        $stockByCategory = $this->stockByCategory();
        $selectedCategoryId = (int) old('kategori_id', $challenge->kategori_id);
        $stockStats = QuestionBank::statisticsForCategory($selectedCategoryId ?: null);

        return view('managemenchallenge.challenge.edit', compact(
            'challenge',
            'categories',
            'stockByCategory',
            'stockStats',
            'selectedCategoryId'
        ));
    }

    /**
     * Stok bank soal aktif per kategori (total + per tingkat + jumlah baru),
     * dikirim ke view agar kartu & batas input level ikut kategori terpilih
     * tanpa reload (JS memakai data ini saat dropdown kategori berubah).
     * Key "all" = seluruh kategori, dipakai saat kategori belum dipilih.
     *
     * @return array<int|string, array{total: int, recent: int, easy: int, medium: int, hard: int}>
     */
    private function stockByCategory(): array
    {
        $map = ['all' => $this->flattenStats(QuestionBank::statisticsForCategory(null))];

        foreach (Category::active()->orderBy('name')->get() as $category) {
            $map[(int) $category->id] = $this->flattenStats(
                QuestionBank::statisticsForCategory((int) $category->id)
            );
        }

        return $map;
    }

    /**
     * @param array<string, array{total: int, recent: int}> $stats
     * @return array{total: int, recent: int, easy: int, medium: int, hard: int}
     */
    private function flattenStats(array $stats): array
    {
        return [
            'total'  => $stats['total']['total'],
            'recent' => $stats['total']['recent'],
            'easy'   => $stats['easy']['total'],
            'medium' => $stats['medium']['total'],
            'hard'   => $stats['hard']['total'],
        ];
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $validated = $this->validateChallenge($request);

        $counts = [
            'easy' => (int) $validated['questions_easy'],
            'medium' => (int) $validated['questions_medium'],
            'hard' => (int) $validated['questions_hard'],
        ];

        $this->assertStockEnough((int) $validated['kategori_id'], $counts);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['starts_at'] = $this->parseLocalDateTime($validated['starts_at'] ?? null);
        $validated['ends_at']   = $this->parseLocalDateTime($validated['ends_at'] ?? null);

        $countsChanged = $challenge->questions_easy !== $counts['easy']
            || $challenge->questions_medium !== $counts['medium']
            || $challenge->questions_hard !== $counts['hard']
            || (int) $challenge->kategori_id !== (int) $validated['kategori_id'];

        DB::transaction(function () use ($challenge, $validated, $counts, $countsChanged) {
            $challenge->update($validated);

            // Susun ulang set hanya bila komposisi/kategori berubah.
            if ($countsChanged) {
                $this->challengeQuestionService->buildSet($challenge, $counts);
            }
        });

        return redirect()->route('challenges.index')
            ->with('success', $countsChanged
                ? 'Challenge diperbarui dan set soal disusun ulang.'
                : 'Challenge berhasil diperbarui.');
    }

    /**
     * Hapus PERMANEN satu challenge beserta set soal & attempt-nya.
     * Ditolak bila sudah ada attempt peserta.
     */
    public function destroy(Challenge $challenge): RedirectResponse
    {
        if ($challenge->isUsed()) {
            return back()->with('error', 'Challenge ini tidak bisa dihapus karena sudah ada peserta yang mengerjakan.');
        }

        $challenge->delete();

        return redirect()->route('challenges.index')
            ->with('success', 'Challenge berhasil dihapus.');
    }

    /**
     * @throws ValidationException bila stok bank soal kurang
     */
    private function assertStockEnough(int $categoryId, array $counts): void
    {
        $errors = $this->challengeQuestionService->validateRequestedCounts($categoryId, $counts);

        if ($errors !== []) {
            throw ValidationException::withMessages(
                collect($errors)->mapWithKeys(fn ($message, $difficulty) => ["questions_{$difficulty}" => $message])->all()
            );
        }
    }

    private function validateChallenge(Request $request): array
    {
        return $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'title'                 => 'required|string|max:200',
            'description'           => 'required|string',
            'duration_minutes'      => 'required|integer|min:1|max:10080',
            'questions_easy'        => 'required|integer|min:0|max:200',
            'questions_medium'      => 'required|integer|min:0|max:200',
            'questions_hard'        => 'required|integer|min:0|max:200',
            'is_active'             => 'boolean',
            'starts_at'             => 'nullable|date_format:Y-m-d\TH:i',
            'ends_at'               => 'nullable|date_format:Y-m-d\TH:i|after:starts_at',
        ], [
            'questions_easy.required' => 'Jumlah soal tingkat mudah wajib diisi.',
            'questions_medium.required' => 'Jumlah soal tingkat sedang wajib diisi.',
            'questions_hard.required' => 'Jumlah soal tingkat sulit wajib diisi.',
        ]);
    }

    private function parseLocalDateTime(?string $value): ?\Carbon\Carbon
    {
        if (empty($value)) return null;
        return \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $value, config('app.timezone'))->setTimezone('UTC');
    }
}
