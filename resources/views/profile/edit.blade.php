<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 light:text-gray-800 leading-tight">
            {{ __('Edit Profil') }}
        </h2>
    </x-slot>

    <div class="flex flex-col gap-4 max-w-[1100px] mx-auto">

        <div class="flex items-center gap-3">
            <a href="{{ route('profile.index') }}"
               class="w-8 h-8 border border-[#262a3d] light:border-[#e5e7eb] bg-[#161822] light:bg-white flex items-center justify-center text-gray-400 light:text-[#6b7568] hover:text-gray-200 light:hover:text-[#1a2e28] hover:bg-[#1c1e2c] light:hover:bg-[#f3f4f6] transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <p class="text-[15px] font-semibold text-slate-50 light:text-[#1a2e28]">Edit Profil</p>
                <p class="text-[12px] text-gray-500 light:text-[#6b7568]">Perbarui identitas, foto, dan keamanan akun Anda.</p>
            </div>
        </div>

        {{-- Alert Status --}}
        @if(session('status') === 'profile-updated')
            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-[#30A192]/10 light:bg-[#dcfce7] border border-[#30A192]/30 light:border-[#86efac] text-[13px] text-[#30A192] light:text-[#166534]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Profil berhasil diperbarui.
            </div>
        @endif

         @if(session('status') === 'photo-updated')
            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-[#30A192]/10 light:bg-[#dcfce7] border border-[#30A192]/30 light:border-[#86efac] text-[13px] text-[#30A192] light:text-[#166534]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Foto profil berhasil diperbarui.
            </div>
        @endif
        
        @if(session('status') === 'password-updated')
            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-[#30A192]/10 light:bg-[#dcfce7] border border-[#30A192]/30 light:border-[#86efac] text-[13px] text-[#30A192] light:text-[#166534]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Password berhasil diperbarui.
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="flex items-start gap-2 px-3.5 py-2.5 bg-red-500/10 light:bg-[#fee2e2] border border-red-500/30 light:border-[#fca5a5] text-[13px] text-red-300 light:text-[#7f1d1d]">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-4 items-start">

            {{-- Kolom Kiri: Foto Profil --}}
            <div class="bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] overflow-hidden">
                <div class="px-4 py-2.5 border-b border-[#1e2130] light:border-[#e5e7eb] bg-[#1a1c28] light:bg-[#f9fafb]">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 light:text-[#4a4a4a]">Foto_Profil</span>
                </div>
                <div class="p-4 flex flex-col gap-3">
                    <div class="w-full aspect-square border border-[#30A192]/60 bg-[#0f1017] light:bg-[#ECECEC] flex items-center justify-center overflow-hidden">
                        @if(Auth::user()->image)
                            <img src="{{ Auth::user()->image_url }}"
                                 alt="{{ Auth::user()->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-5xl font-bold text-[#30A192] font-mono">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>

                    <div class="text-center text-[12px] font-mono text-gray-400 light:text-[#6b7568]">
                        {{ '@' . Auth::user()->username }}
                    </div>

                    <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="flex items-center justify-center gap-2 h-9 bg-[#30A192] text-[#0d0e12] text-[12px] font-semibold hover:opacity-90 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/>
                            </svg>
                            Ganti Foto
                            <input type="file" name="image" class="hidden" accept="image/*" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="flex flex-col gap-4">

                {{-- Identitas Personal --}}
                <form method="POST" action="{{ route('profile.update') }}" id="identitas-form">
                    @csrf
                    @method('PATCH')

                    <div class="bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-[#1e2130] light:border-[#e5e7eb] bg-[#1a1c28] light:bg-[#f9fafb]">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 light:text-[#4a4a4a]">Identitas_Personal</span>
                        </div>

                        <div class="p-4 flex flex-col gap-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Username --}}
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Username</label>
                                    <input
                                        type="text"
                                        name="username"
                                        value="{{ old('username', Auth::user()->username) }}"
                                        required
                                        class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('username') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] outline-none focus:border-[#30A192] transition"
                                    />
                                    @error('username')
                                        <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', Auth::user()->email) }}"
                                        required
                                        class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('email') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] outline-none focus:border-[#30A192] transition"
                                    />
                                    @error('email')
                                        <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nama --}}
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Nama Pengguna</label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', Auth::user()->name) }}"
                                    required
                                    placeholder="Nama Pengguna"
                                    class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('name') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] placeholder:text-gray-600 light:placeholder:text-[#9ca39c] outline-none focus:border-[#30A192] transition"
                                />
                                @error('name')
                                    <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Keamanan --}}
                <form method="POST" action="{{ route('password.update') }}" id="password-form">
                    @csrf
                    @method('PUT')

                    <div class="bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-[#1e2130] light:border-[#e5e7eb] bg-[#1a1c28] light:bg-[#f9fafb]">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 light:text-[#4a4a4a]">Keamanan</span>
                        </div>

                        <div class="p-4 flex flex-col gap-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                {{-- Password Lama --}}
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Password Saat Ini</label>
                                    <input
                                        type="password"
                                        name="current_password"
                                        autocomplete="current-password"
                                        placeholder="Lama"
                                        class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('current_password') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] placeholder:text-gray-600 light:placeholder:text-[#9ca39c] outline-none focus:border-[#30A192] transition"
                                    />
                                    @error('current_password', 'updatePassword')
                                        <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Password Baru --}}
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Password Baru</label>
                                    <input
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        placeholder="Baru"
                                        class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('password') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] placeholder:text-gray-600 light:placeholder:text-[#9ca39c] outline-none focus:border-[#30A192] transition"
                                    />
                                    @error('password', 'updatePassword')
                                        <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                {{-- Konfirmasi --}}
                                <div>
                                    <label class="block text-[11px] font-medium text-gray-500 light:text-[#6b7568] mb-1.5 uppercase tracking-wide">Konfirmasi Password</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        autocomplete="new-password"
                                        placeholder="Konfirmasi"
                                        class="w-full h-9 bg-[#0f1017] light:bg-[#ECECEC] border {{ $errors->has('password_confirmation') ? 'border-red-500/60' : 'border-[#262a3d] light:border-[#e5e7eb]' }} px-3 text-[13px] text-slate-100 light:text-[#1a2e28] placeholder:text-gray-600 light:placeholder:text-[#9ca39c] outline-none focus:border-[#30A192] transition"
                                    />
                                    @error('password_confirmation', 'updatePassword')
                                        <span class="flex items-center gap-1.5 text-[11px] text-red-400 mt-1.5">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-[#1e2130] light:border-[#e5e7eb] pt-3">
                                <p class="text-[11px] text-gray-500 light:text-[#6b7568]">
                                    Status: Terakhir diubah {{ Auth::user()->updated_at?->diffForHumans() ?? 'belum pernah' }}
                                </p>

<button type="submit"
                                    class="inline-flex items-center gap-1.5 h-8 px-4 bg-[#30A192] text-[#0d0e12] text-[12px] font-semibold uppercase hover:opacity-90 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/>
                                    </svg>
                                    PERBARUI PASSWORD
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Actions --}}
                <div class="bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] overflow-hidden">
                    <div class="flex items-center justify-end gap-2 px-4 py-3">
                        <a href="{{ route('profile.index') }}"
                            class="inline-flex items-center justify-center h-8 px-4 bg-white/5 light:bg-black/5 text-gray-300 light:text-[#4a4a4a] text-[12px] font-semibold hover:bg-white/10 light:hover:bg-black/10 transition">
                            BATAL
                        </a>
                        <button type="submit" form="identitas-form"
                            class="inline-flex items-center gap-1.5 h-8 px-4 bg-[#30A192] text-[#0d0e12] text-[12px] font-semibold hover:opacity-90 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-6 4h6m-6 4h6M9 7V3h6v4"/>
                            </svg>
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>