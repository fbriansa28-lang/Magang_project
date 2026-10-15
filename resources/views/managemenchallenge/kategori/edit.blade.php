<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA KATEGORI</h2></x-slot>

    @can('edit kategori')
    <div class="mx-auto mt-[80px] w-full max-w-[620px] font-mono">
        <section class="overflow-hidden border border-[#263239] border-t-4 border-t-[#c86cff] bg-[#0d0e12] light:border-[#263239] light:border-t-[#003033] light:bg-white">
            <div class="px-5 py-5 light:bg-white">
                <h1 class="text-[24px] font-bold tracking-tight text-gray-200 light:text-[#003033]">EDIT_KATEGORI</h1>
                <form method="POST" action="{{ route('kategori.update', $challengeCategory) }}" class="mt-6">
                    @csrf @method('PUT')
                    <label for="name" class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Nama Kategori</label>
                    <input id="name" value="{{ $challengeCategory->name }}" readonly class="h-[62px] w-full border border-[#3b494b] bg-[#0d0e12] px-4 text-[16px] text-gray-500 outline-none light:bg-[#ECECEC] light:text-[#003033]">
                    <label for="weight_score" class="mb-2 mt-5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Bobot Skor</label>
                    <input id="weight_score" name="weight_score" type="number" min="1" max="100" value="{{ old('weight_score', $challengeCategory->weight_score) }}" placeholder="Tulis Bobot Skor disini...." required class="h-[62px] w-full border border-[#3b494b] bg-[#0d0e12] px-4 text-[13px] text-gray-200 outline-none placeholder:text-gray-400 focus:border-[#14f0e6] light:bg-[#ECECEC] light:text-[#003033] light:placeholder:text-gray-600">
                    @error('weight_score')<p class="mt-2 text-[11px] text-rose-400">{{ $message }}</p>@enderror
                    <div class="mt-6 flex justify-end gap-5"><a href="{{ route('kategori.index') }}" class="flex h-[32px] w-[115px] items-center justify-center border border-[#ff5f57] bg-[#4b1115] text-[10px] font-bold uppercase tracking-[0.14em] text-[#ffd7d7] hover:bg-[#64171d]">BATAL</a><button type="submit" class="flex h-[32px] w-[170px] items-center justify-center gap-1.5 border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] shadow-[0_0_18px_rgba(20,240,230,0.15)] hover:bg-[#174b96]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN PERUBAHAN</button></div>
                </form>
            </div>
        </section>
    </div>
    @endcan
</x-app-layout>
