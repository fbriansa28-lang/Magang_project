<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 light:text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="flex justify-center w-full">

        <div class="w-full max-w-lg bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] p-6">

            {{-- Avatar --}}
            <div class="flex justify-center mb-5">
                <div class="w-[140px] h-[140px] overflow-hidden border border-[#30A192]/60 bg-[#f3f7f5] light:bg-[#f3f7f5] flex items-center justify-center">
                    @if(Auth::user()->image)
                        <img src="{{ Auth::user()->image_url }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-[#30A192] font-mono">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Button Edit Profil --}}
            <div class="flex justify-center mb-8">
                <a href="{{ route('profile.edit') }}"
                   class="inline-block text-center w-36 py-2 text-[#0d0e12] text-[11px] font-bold tracking-widest bg-[#30A192] hover:opacity-90 transition">
                    EDIT PROFIL
                </a>
            </div>

            {{-- Detail Profile --}}
            <div class="flex flex-col gap-4">

                {{-- Username --}}
                <div>
                    <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] font-sans">
                        USERNAME
                    </label>
                    <input type="text"
                           value="{{ Auth::user()->username }}"
                           readonly
                           class="w-full box-border bg-[#f3f7f5] light:bg-[#f3f7f5] border border-[#dbe6e2] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-200 light:text-[#1a2e28] outline-none">
                </div>

                {{-- Nama --}}
                <div>
                    <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] font-sans">
                            NAMA
                    </label>
                    <input type="text"
                           value="{{ Auth::user()->name }}"
                           readonly
                           class="w-full box-border bg-[#f3f7f5] light:bg-[#f3f7f5] border border-[#dbe6e2] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-200 light:text-[#1a2e28] outline-none">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] uppercase font-sans">
                        Email
                    </label>
                    <input type="email"
                           value="{{ Auth::user()->email }}"
                           readonly
                           class="w-full box-border bg-[#f3f7f5] light:bg-[#f3f7f5] border border-[#dbe6e2] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-200 light:text-[#1a2e28] outline-none">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] uppercase font-sans">
                        Password
                    </label>
                    <input type="password"
                           value="**************"
                           readonly
                           class="w-full box-border bg-[#f3f7f5] light:bg-[#f3f7f5] border border-[#dbe6e2] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-200 light:text-[#1a2e28] outline-none">
                </div>

            </div>
        </div>
    </div>
</x-app-layout>