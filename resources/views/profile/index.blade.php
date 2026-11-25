<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 light:text-gray-800 leading-tight">
            {{ __('PROFIL') }}
        </h2>
    </x-slot>

    @php
        $user = Auth::user();

        $fieldClass = 'w-full box-border bg-[#0f1017] light:bg-[#f3f7f5] border border-[#262a3d] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-100 light:text-[#1a2e28] outline-none pointer-events-none select-none cursor-default';

        $fields = [
            ['label' => 'Username', 'type' => 'text',     'value' => $user->username],
            ['label' => 'Nama',     'type' => 'text',     'value' => $user->name],
            ['label' => 'Email',    'type' => 'email',    'value' => $user->email],
            ['label' => 'Password', 'type' => 'password', 'value' => '**************'],
        ];
    @endphp

    <div class="flex justify-center w-full">

        <div class="w-full max-w-lg bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] p-6">

            {{-- Avatar --}}
            <div class="flex justify-center mb-5">
                <div class="w-[140px] h-[140px] overflow-hidden border border-[#30A192]/60 bg-[#0f1017] light:bg-[#f3f7f5] flex items-center justify-center">
                    @if($user->image)
                        <img src="{{ $user->image_url }}"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-[#30A192] font-mono">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
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

            {{-- Detail Profil (read only) --}}
            <div class="flex flex-col gap-4">
                @foreach($fields as $field)
                    <div>
                        <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] uppercase font-sans">
                            {{ $field['label'] }}
                        </label>
                        <input type="{{ $field['type'] }}"
                               value="{{ $field['value'] }}"
                               readonly
                               tabindex="-1"
                               aria-readonly="true"
                               class="{{ $fieldClass }}">
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>