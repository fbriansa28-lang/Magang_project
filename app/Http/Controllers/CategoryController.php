<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view kategori', only: ['index']),
            new Middleware('permission:create kategori', only: ['create', 'store']),
            new Middleware('permission:edit kategori', only: ['edit', 'update']),
            new Middleware('permission:delete kategori', only: ['destroy']),
        ];
    }

    /**
     * Lihat daftar kategori.
     */
    public function index(): View
    {
        $categories = Category::getAllKategori(10);

        return view('managemenchallenge.kategori.index', compact('categories'));
    }

    public function create(): View
    {
        return view('managemenchallenge.kategori.create');
    }

    /**
     * Tambah kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'weight_score' => 'required|integer|min:1|max:100',
        ], [
            'name.required'         => 'Nama kategori wajib diisi.',
            'name.max'              => 'Nama kategori maksimal 50 karakter.',
            'weight_score.required' => 'Bobot skor wajib diisi.',
            'weight_score.integer'  => 'Bobot skor harus berupa angka.',
            'weight_score.min'      => 'Bobot skor minimal 1.',
            'weight_score.max'      => 'Bobot skor maksimal 100.',
        ]);

        if (Category::cekKategoriExist($validated['name'])) {
            return back()->withInput()
                ->with('error', 'Kategori sudah ada.');
        }

        Category::simpanKategori($validated['name'], (int) $validated['weight_score']);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori challenge berhasil dibuat.');
    }

    public function edit(string $kategori_id): View
    {
        $kategori = Category::findOrFail((int) $kategori_id);

        return view('managemenchallenge.kategori.edit', ['challengeCategory' => $kategori]);
    }

    /**
     * Edit bobot skor kategori.
     */
    public function update(string $kategori_id, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'weight_score' => 'required|integer|min:1|max:100',
        ], [
            'weight_score.required' => 'Bobot skor wajib diisi.',
            'weight_score.integer'  => 'Bobot skor harus berupa angka.',
            'weight_score.min'      => 'Bobot skor minimal 1.',
            'weight_score.max'      => 'Bobot skor maksimal 100.',
        ]);

        Category::updateKategori((int) $kategori_id, (int) $validated['weight_score']);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori challenge berhasil diperbarui.');
    }

    /**
     * Hapus kategori.
     */
    public function destroy(string $kategori_id): RedirectResponse
    {
        if (Category::cekKategoriDipakai((int) $kategori_id)) {
            return back()->with('error', 'Kategori masih digunakan, tidak dapat dihapus.');
        }

        Category::deleteKategori((int) $kategori_id);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori challenge berhasil dihapus.');
    }
}
