@php
    $isEdit = isset($challenge);
    $curDuration = old('duration_minutes', $challenge->duration_minutes ?? 30);
    $curIsActive = old('is_active', $challenge->is_active ?? true) ? 1 : 0;
    $levelInputs = [
        'easy' => ['label' => 'MUDAH', 'field' => 'questions_easy'],
        'medium' => ['label' => 'SEDANG', 'field' => 'questions_medium'],
        'hard' => ['label' => 'SULIT', 'field' => 'questions_hard'],
    ];
@endphp

@if($errors->any())
    <div class="mb-4 flex items-start gap-2 border border-red-500/30 bg-red-500/10 px-4 py-3 text-[12px] text-red-300 light:bg-red-50 light:text-red-700">
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <ul class="list-inside list-disc space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Ikon kalender datetime-local: putih di dark mode, tetap gelap di light mode. --}}
<style>
    input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
    .light input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: none;
    }
</style>

{{-- Card form: accent bar atas pink + heading besar (persis gambar). --}}
<section class="border border-[#263239] bg-[#12161c] px-8 py-7 pb-16 light:border-[#3b494b] light:bg-white" style="border-top:4px solid #f0b7ff;">
    <h1 class="mb-7 text-[26px] font-extrabold uppercase tracking-tight text-slate-50 light:text-[#003033]">
        {{ $isEdit ? 'EDIT_CHALLENGE' : 'BUAT_CHALLENGE' }}
    </h1>

    <div class="flex flex-col gap-5">
        {{-- JUDUL CHALLENGE --}}
        <div>
            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Judul Challenge</label>
            <div class="cs-editor border border-[#3b494b] bg-[#0f1017] light:bg-[#ECECEC]">
                <div class="flex h-7 items-center gap-1 border-b border-[#3b494b] px-2">
                    <button type="button" onclick="csWrap(this,'**','**')" class="h-5 w-5 text-[11px] font-bold text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">B</button>
                    <button type="button" onclick="csWrap(this,'*','*')" class="h-5 w-5 text-[11px] italic text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">I</button>
                    <button type="button" onclick="csWrap(this,'`','`')" class="h-5 w-5 text-[11px] text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">&lt;/&gt;</button>
                </div>
                <textarea name="title" rows="2" required placeholder="Tulis judul challenge disini..."
                          class="w-full resize-y border-0 bg-transparent px-3 py-2.5 text-[13px] text-slate-100 outline-none placeholder:text-gray-600 light:text-[#1a2e28] light:placeholder:text-[#9ca39c]">{{ old('title', $challenge->title ?? '') }}</textarea>
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div>
            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Deskripsi</label>
            <div class="cs-editor border border-[#3b494b] bg-[#0f1017] light:bg-[#ECECEC]">
                <div class="flex h-7 items-center gap-1 border-b border-[#3b494b] px-2">
                    <button type="button" onclick="csWrap(this,'**','**')" class="h-5 w-5 text-[11px] font-bold text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">B</button>
                    <button type="button" onclick="csWrap(this,'*','*')" class="h-5 w-5 text-[11px] italic text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">I</button>
                    <button type="button" onclick="csWrap(this,'`','`')" class="h-5 w-5 text-[11px] text-gray-400 hover:text-gray-200 light:text-[#6b7568] light:hover:text-[#1a2e28]">&lt;/&gt;</button>
                </div>
                <textarea name="description" rows="3" required placeholder="Tulis Deskripsi challenge disini..."
                          class="w-full resize-y border-0 bg-transparent px-3 py-2.5 text-[13px] text-slate-100 outline-none placeholder:text-gray-600 light:text-[#1a2e28] light:placeholder:text-[#9ca39c]">{{ old('description', $challenge->description ?? '') }}</textarea>
            </div>
        </div>

        {{-- KATEGORI / AWAL / AKHIR --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Kategori</label>
                <div class="relative">
                    <select name="kategori_id" required
                            class="h-11 w-full appearance-none border border-[#3b494b] bg-[#0f1017] px-3 pr-9 text-[11px] font-bold uppercase tracking-wide text-gray-200 outline-none focus:border-[#30A192] light:bg-[#ECECEC] light:text-[#003033]">
                        <option value="">KATEGORI</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('kategori_id', $challenge->kategori_id ?? '') == $category->id)>{{ strtoupper($category->name) }}</option>
                        @endforeach
                    </select>
                    </div>
            </div>
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Awal Aktif Challenge</label>
                <div class="relative">
                    <input type="datetime-local" name="starts_at"
                           value="{{ old('starts_at', isset($challenge) && $challenge->starts_at ? $challenge->starts_at->timezone(config('app.timezone'))->format('Y-m-d\TH:i') : '') }}"
                           class="h-11 w-full border border-[#3b494b] bg-[#0f1017] px-3 text-[11px] text-gray-200 outline-none focus:border-[#30A192] light:bg-[#ECECEC] light:text-[#003033]">
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Akhir Aktif Challenge</label>
                <div class="relative">
                    <input type="datetime-local" name="ends_at"
                           value="{{ old('ends_at', isset($challenge) && $challenge->ends_at ? $challenge->ends_at->timezone(config('app.timezone'))->format('Y-m-d\TH:i') : '') }}"
                           class="h-11 w-full border border-[#3b494b] bg-[#0f1017] px-3 text-[11px] text-gray-200 outline-none focus:border-[#30A192] light:bg-[#ECECEC] light:text-[#003033]">
                </div>
            </div>
        </div>

        {{-- LEVEL: jumlah soal per tingkat, batas maksimum = stok bank soal kategori terpilih --}}
        <div>
            <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Level</label>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach($levelInputs as $key => $level)
                    <div class="border border-[#3b494b] bg-[#0f1017] light:bg-[#ECECEC]">
                        <div class="flex h-11 items-center justify-between gap-3 px-3">
                            <span class="text-[11px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">{{ $level['label'] }}</span>
                            <input type="number" min="0" required
                                   name="{{ $level['field'] }}"
                                   data-stock-input="{{ $key }}"
                                   data-stock-label="{{ $level['label'] }}"
                                   data-stock-max="{{ $stockStats[$key]['total'] ?? 0 }}"
                                   value="{{ old($level['field'], $challenge->{$level['field']} ?? 0) }}"
                                   class="h-7 w-20 border border-[#3b494b] bg-transparent px-2 text-center text-[11px] font-bold text-gray-200 outline-none light:text-[#003033]">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Peringatan stok: elemen terpisah DI BAWAH kontainer level,
                 muncul hanya bila jumlah diminta melebihi stok bank soal. --}}
            <div id="cs-stock-warning" class="mt-2 hidden text-[10px] font-bold text-red-500 light:text-red-600">
                <ul id="cs-stock-warning-list" class="list-inside list-disc space-y-0.5"></ul>
            </div>
        </div>

        {{-- DURASI + STATUS: durasi kecil di kiri, checklist aktif di kanan --}}
        <div class="flex flex-wrap items-end gap-5">
            <div>
                <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Durasi/Menit</label>
                <div class="flex h-11 items-center border border-[#3b494b] bg-[#0f1017] px-3 light:bg-[#ECECEC]">
                    <input type="number" min="1" max="10080" required
                           name="duration_minutes"
                           value="{{ $curDuration }}"
                           class="h-7 w-16 border-0 bg-transparent px-0 text-center text-[11px] font-bold text-gray-200 outline-none focus:ring-0 light:text-[#003033]">
                </div>
            </div>
            <label class="flex h-11 cursor-pointer items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked($curIsActive) class="h-3.5 w-3.5 accent-[#30A192]">
                Aktif
            </label>
        </div>

        {{-- AKSI --}}
        <div class="mt-3 flex justify-end gap-3">
            <a href="{{ route('challenges.index') }}"
class="border border-[#ff5f57] bg-[#4b1115] px-5 py-2 text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">BATAL</a>
             <button type="submit"
                     class="inline-flex items-center gap-1.5 border border-[#4f8cff] bg-[#123b78] px-5 py-2 text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN</button>
        </div>
    </div>
</section>

<script>
    // Toolbar B/I/<> sederhana untuk textarea.
    function csWrap(button, open, close) {
        const editor = button.closest('.cs-editor');
        const textarea = editor?.querySelector('textarea');
        if (!textarea) return;

        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const value = textarea.value;
        const selected = value.slice(start, end) || 'teks';

        textarea.value = value.slice(0, start) + open + selected + close + value.slice(end);
        textarea.focus();
    }

    // Peringatan stok: muncul HANYA bila jumlah diminta > stok, dirender
    // sebagai satu blok terpisah di bawah kontainer level.
    function csUpdateStockWarnings() {
        const warningBox = document.getElementById('cs-stock-warning');
        const warningList = document.getElementById('cs-stock-warning-list');
        if (!warningBox || !warningList) return;

        const messages = [];

        document.querySelectorAll('[data-stock-input]').forEach((input) => {
            const label = input.dataset.stockLabel || input.dataset.stockInput;
            const max = parseInt(input.dataset.stockMax || '0', 10);
            const value = parseInt(input.value || '0', 10) || 0;

            if (value > max) {
                messages.push(`${label}: stok kurang! Tersedia ${max} soal, diminta ${value}.`);
            }
        });

        warningList.innerHTML = '';

        messages.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            warningList.appendChild(item);
        });

        warningBox.classList.toggle('hidden', messages.length === 0);
    }

    // Kartu stok + batas input level mengikuti kategori terpilih.
    // Kategori belum dipilih -> pakai stok seluruh kategori (key "all").
    function csApplyStock(categoryId) {
        const map = window.csStockByCategory || {};
        const values = map[categoryId] || map.all || { total: 0, recent: 0, easy: 0, medium: 0, hard: 0 };

        ['total', 'easy', 'medium', 'hard'].forEach((key) => {
            const valueEl = document.getElementById(key === 'total' ? 'stat-total-questions' : `stat-${key}`);

            if (valueEl) valueEl.textContent = values[key] ?? 0;
        });

        document.querySelectorAll('[data-stock-input]').forEach((input) => {
            const key = input.dataset.stockInput;
            input.dataset.stockMax = values[key] ?? 0;
        });

        csUpdateStockWarnings();
    }

    const categorySelect = document.querySelector('select[name="kategori_id"]');
    categorySelect?.addEventListener('change', () => csApplyStock(categorySelect.value));

    document.querySelectorAll('[data-stock-input]').forEach((input) => {
        input.addEventListener('input', csUpdateStockWarnings);
    });

    csApplyStock(categorySelect?.value || '');
    csUpdateStockWarnings();
</script>
