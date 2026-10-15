<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA KATEGORI</h2></x-slot>

    @can('create kategori')
    <div class="mx-auto mt-[80px] w-full max-w-[620px] font-mono">
        <section class="overflow-hidden border border-[#263239] border-t-4 border-t-[#c86cff] bg-[#0d0e12] light:border-[#263239] light:border-t-[#003033] light:bg-white">
            <div class="px-5 py-5 light:bg-white">
                <h1 class="text-[24px] font-bold tracking-tight text-gray-200 light:text-[#003033]">BUAT_KATEGORI</h1>
                <form method="POST" action="{{ route('kategori.store') }}" class="mt-6">
                    @csrf
                    <label for="name" class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Nama Kategori</label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="Tulis Nama Kategori...." required class="h-[62px] w-full border border-[#3b494b] bg-[#0d0e12] px-4 text-[13px] text-gray-200 outline-none placeholder:text-gray-500 focus:border-[#14f0e6] light:bg-[#ECECEC] light:text-[#003033] light:placeholder:text-gray-600">
                    @error('name')<p class="mt-2 text-[11px] text-rose-400">{{ $message }}</p>@enderror
                    <label for="weight_score" class="mb-2 mt-5 block text-[10px] font-bold uppercase tracking-[0.14em] text-gray-300 light:text-[#003033]">Bobot Skor</label>
                    <input id="weight_score" name="weight_score" type="number" min="1" max="100" value="{{ old('weight_score') }}" placeholder="Tulis Bobot Skor disini...." required class="h-[62px] w-full border border-[#3b494b] bg-[#0d0e12] px-4 text-[13px] text-gray-200 outline-none placeholder:text-gray-500 focus:border-[#14f0e6] light:bg-[#ECECEC] light:text-[#003033] light:placeholder:text-gray-600">
                    @error('weight_score')<p class="mt-2 text-[11px] text-rose-400">{{ $message }}</p>@enderror
                    <div class="mt-6 flex justify-end gap-5"><a href="{{ route('kategori.index') }}" class="flex h-[32px] w-[115px] items-center justify-center border border-[#ff5f57] bg-[#4b1115] text-[10px] font-bold uppercase tracking-[0.14em] text-[#ffd7d7] hover:bg-[#64171d]">BATAL</a><button type="submit" class="flex h-[32px] w-[170px] items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] shadow-[0_0_18px_rgba(20,240,230,0.15)] hover:bg-[#174b96]">BUAT KATEGORI</button></div>
                </form>
            </div>
        </section>
    </div>
    @endcan
</x-app-layout>
