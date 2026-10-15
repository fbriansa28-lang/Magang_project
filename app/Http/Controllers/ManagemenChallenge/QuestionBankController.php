<?php

namespace App\Http\Controllers\ManagemenChallenge;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\QuestionBank;
use App\Traits\HandlesMediaUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Bank Soal (sumber soal terpusat untuk set tetap challenge):
 * - store()     : tambah soal (validasi -> cekDuplikat -> simpan)
 * - edit()      : ambil detail soal -> tampilkan form edit terisi
 * - update()    : simpan perubahan soal
 * - destroy()   : hapus soal (dengan cek penggunaan -> sarankan nonaktifkan)
 * - statistik() : statistik bank soal per kesulitan + penanda stok kritis
 */
class QuestionBankController extends Controller implements HasMiddleware
{
    use HandlesMediaUploads;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view bank soal', only: ['index']),
            new Middleware('permission:create bank soal', only: ['create', 'store']),
            new Middleware('permission:edit bank soal', only: ['edit', 'update']),
            new Middleware('permission:delete bank soal', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = QuestionBank::with('category')->withCount('attemptQuestions');

        if ($request->filled('category'))   $query->where('kategori_id', $request->category);
        if ($request->filled('difficulty')) $query->where('difficulty', $request->difficulty);
        if ($request->filled('search'))     $query->where('content', 'like', '%'.$request->search.'%');

        $questionBanks = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        $statistics = $this->statistik();
        $totalStatistic = QuestionBank::totalStatistics();

        return view('managemenchallenge.questionbank.index', compact('questionBanks', 'categories', 'statistics', 'totalStatistic'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('managemenchallenge.questionbank.create', compact('categories'));
    }

    /**
     * Jika soal dengan isi yang sama sudah ada dan admin belum
     * mengonfirmasi (confirm_duplicate=1), tampilkan peringatan alih-alih
     * langsung menyimpan (sesuai alt "soal sudah ada (duplikat)").
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateQuestion($request);
        $options = $this->extractOptions($validated);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();

        $duplicate = QuestionBank::cekDuplikat($validated['content']);
        if ($duplicate && !$request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('duplicate_warning', [
                'id'      => $duplicate->id,
                'content' => $duplicate->content,
            ]);
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->storeImage($request->file('image'), 'bank-soal');
        }

        $question = QuestionBank::simpan($validated);
        $question->syncOptions($options);

        return redirect()->route('bank-soal.index')->with('success', 'Soal berhasil ditambahkan ke Bank Soal.');
    }

    /**
     * edit(soal_id): ambil detail soal lalu tampilkan form edit terisi.
     */
    public function edit(string $soal_id): View
    {
        $bankSoal = QuestionBank::findById((int) $soal_id);
        $bankSoal->load('optionRows');
        $categories = Category::active()->orderBy('name')->get();
        $usageCount = QuestionBank::cekPenggunaan((int) $soal_id);

        return view('managemenchallenge.questionbank.edit', [
            'question'   => $bankSoal,
            'categories' => $categories,
            'usageCount' => $usageCount,
        ]);
    }

    /**
     * update(soal_id, soalDataBaru): validasi -> cekDuplikat -> update.
     */
    public function update(string $soal_id, Request $request): RedirectResponse
    {
        $bankSoal = QuestionBank::findById((int) $soal_id);

        $validated = $this->validateQuestion($request);
        $options = $this->extractOptions($validated);
        $validated['is_active'] = $request->boolean('is_active');

        $duplicate = QuestionBank::cekDuplikat($validated['content'], (int) $soal_id);
        if ($duplicate && !$request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('duplicate_warning', [
                'id'      => $duplicate->id,
                'content' => $duplicate->content,
            ]);
        }

        if ($request->hasFile('image')) {
            $this->deleteMedia($bankSoal->image_path);
            $validated['image_path'] = $this->storeImage($request->file('image'), 'bank-soal');
        } elseif ($request->input('remove_image') === '1') {
            $this->deleteMedia($bankSoal->image_path);
            $validated['image_path'] = null;
        }

        $soal = QuestionBank::updateSoal((int) $soal_id, $validated);
        $soal->syncOptions($options);

        return redirect()->route('bank-soal.index')->with('success', 'Soal Bank Soal berhasil diperbarui.');
    }

    /**
     * destroy(soal_id): cekPenggunaan(soal_id) -> jika masih dipakai di
     * challenge/task, tolak hapus permanen dan arahkan admin untuk
     * menonaktifkan saja; jika tidak dipakai, hapus dari bank soal.
     */
    public function destroy(string $soal_id): RedirectResponse
    {
        $bankSoal = QuestionBank::findById((int) $soal_id);

        $usageCount = QuestionBank::cekPenggunaan((int) $soal_id);
        if ($usageCount > 0) {
            return back()->with('error', 'Soal ini sudah pernah muncul di '.$usageCount.' attempt peserta, tidak bisa dihapus. Nonaktifkan soal ini jika tidak ingin dipakai lagi.');
        }

        $this->deleteMedia($bankSoal->image_path);
        QuestionBank::hapusSoal((int) $soal_id);

        return back()->with('success', 'Soal berhasil dihapus dari Bank Soal.');
    }

    /**
     * statistik(): hitung jumlah soal per difficulty, lalu tandai
     * kategori_kritis (stok < batas minimum) supaya view bisa menampilkan
     * peringatan "stok soal menipis".
     */
    public function statistik(): array
    {
        $statistics = QuestionBank::hitungJumlahPerDifficulty();

        foreach ($statistics as $key => $stat) {
            $statistics[$key]['critical'] = $stat['total'] < QuestionBank::MIN_STOCK_WARNING;
        }

        return $statistics;
    }

    private function validateQuestion(Request $request): array
    {
        $isMc = $request->input('question_type') === QuestionBank::TYPE_PILIHAN_GANDA;

        $validated = $request->validate([
            'kategori_id' => 'nullable|exists:kategori,id',
            'content'        => 'required|string|max:5000',
            'question_type'  => 'required|in:essay,pilihan_ganda',
            'difficulty'     => 'required|in:easy,medium,hard',
            'flag'           => 'required_if:question_type,essay|nullable|string|max:500',
            'option_a'       => 'required_if:question_type,pilihan_ganda|nullable|string|max:255',
            'option_b'       => 'required_if:question_type,pilihan_ganda|nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'option_e'       => 'nullable|string|max:255',
            'correct_option' => 'required_if:question_type,pilihan_ganda|nullable|in:a,b,c,d,e',
            'hints'          => 'nullable|array|max:1',
            'hints.*'        => 'nullable|string|max:255',
            'explanation'    => 'nullable|string|max:2000',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'remove_image'   => 'nullable|in:0,1',
        ]);

        unset($validated['image'], $validated['remove_image']);

        // Beberapa hint (dari tombol "+Add_Hint" di form) digabung jadi satu
        // string dipisah baris baru, karena kolom `hint` di tabel masih
        // berupa string tunggal (lihat resources/views/.../partials/form.blade.php).
        $validated['hint'] = collect($validated['hints'] ?? [])
            ->map(fn ($h) => trim((string) $h))
            ->filter(fn ($h) => $h !== '')
            ->implode("\n");
        unset($validated['hints']);

        $validated['flag']           = !$isMc ? ($validated['flag'] ?? null) : null;
        $validated['option_a']       = $isMc ? ($validated['option_a'] ?? null) : null;
        $validated['option_b']       = $isMc ? ($validated['option_b'] ?? null) : null;
        $validated['option_c']       = $isMc ? ($validated['option_c'] ?? null) : null;
        $validated['option_d']       = $isMc ? ($validated['option_d'] ?? null) : null;
        $validated['option_e']       = $isMc ? ($validated['option_e'] ?? null) : null;
        $validated['correct_option'] = $isMc ? ($validated['correct_option'] ?? null) : null;

        return $validated;
    }

    private function extractOptions(array &$validated): array
    {
        $options = [
            'a' => $validated['option_a'] ?? null,
            'b' => $validated['option_b'] ?? null,
            'c' => $validated['option_c'] ?? null,
            'd' => $validated['option_d'] ?? null,
            'e' => $validated['option_e'] ?? null,
        ];

        unset($validated['option_a'], $validated['option_b'], $validated['option_c'], $validated['option_d'], $validated['option_e']);

        return $options;
    }
}
