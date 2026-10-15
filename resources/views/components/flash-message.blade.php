{{-- Flash message global: success / error / status.
     Dipasang di layouts.app & layouts.guest agar semua halaman otomatis tampil. --}}
@php
    $flashSuccess = session('success');
    $flashError = session('error');
    $flashStatus = session('status');
@endphp

@if ($flashSuccess || $flashError || $flashStatus)
    <div class="mb-4 flex flex-col gap-2 font-mono">
        @if ($flashSuccess)
            <div class="border border-[#30A192]/50 bg-[#30A192]/10 px-4 py-3 text-[11px] text-[#80fff2] light:bg-emerald-50 light:text-emerald-700">
                {{ $flashSuccess }}
            </div>
        @endif

        @if ($flashError)
            <div class="border border-red-400/50 bg-red-950/30 px-4 py-3 text-[11px] text-red-300 light:bg-red-50 light:text-red-700">
                {{ $flashError }}
            </div>
        @endif

        @if ($flashStatus)
            <div class="border border-[#30A192]/50 bg-[#30A192]/10 px-4 py-3 text-[11px] text-[#80fff2] light:bg-emerald-50 light:text-emerald-700">
                {{ $flashStatus }}
            </div>
        @endif
    </div>
@endif
