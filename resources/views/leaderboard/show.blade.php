<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">PAPAN PERINGKAT</h2></x-slot>

    @php
        // Gaya peringkat: rank 1 violet, rank 2 cyan, rank 3 pink, 4+ netral.
        // Bintang: rank 1 = 3, rank 2 = 2, rank 3 = 1, rank 4+ = tanpa bintang.
        $rankStyle = function (int $rank, bool $isMe): array {
            $rankColor = match ($rank) {
                1 => ['text' => 'text-[#a855f7]', 'bar' => 'bg-[#a855f7]', 'glow' => 'from-[#a855f7]/25'],
                2 => ['text' => 'text-[#20c5e2]', 'bar' => 'bg-[#20c5e2]', 'glow' => 'from-[#20c5e2]/25'],
                3 => ['text' => 'text-[#f472b6]', 'bar' => 'bg-[#f472b6]', 'glow' => 'from-[#f472b6]/25'],
                default => ['text' => 'text-gray-200 light:text-[#003033]', 'bar' => 'bg-gray-300 light:bg-[#4b5563]', 'glow' => ''],
            };

            $stars = match ($rank) {
                1 => 3,
                2 => 2,
                3 => 1,
                default => 0,
            };

            if ($isMe) {
                // Baris sendiri: highlight khusus, tapi tetap ikut warna peringkat.
                return [
                    'stars' => $stars,
                    'row' => 'bg-[#0f2a1a] light:bg-[#e8e4c9]',
                    'num' => $rankColor['text'],
                    'score' => $rankColor['text'],
                    'bar' => $rankColor['bar'],
                    'time' => $rankColor['text'],
                ];
            }

            // Baris biasa: base bg standar tabel app + gradien halus untuk top 3.
            $gradient = $rankColor['glow'] !== ''
                ? 'bg-gradient-to-r '.$rankColor['glow'].' to-transparent'
                : '';

            return [
                'stars' => $stars,
                'row' => trim('bg-[#0d0e12] '.$gradient.' hover:bg-white/[0.03] light:bg-white light:hover:bg-black/[0.02]'),
                'num' => $rankColor['text'],
                'score' => $rankColor['text'],
                'bar' => $rankColor['bar'],
                'time' => $rankColor['text'],
            ];
        };
    @endphp

    @php $isAdmin = auth()->user()?->can('show leaderboard'); @endphp

    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono">
        <section class="border border-[#263239] bg-black light:border-[#3b494b] light:bg-white">
            {{-- Header window: traffic lights --}}
            <div class="flex h-7 items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 light:border-[#003033] light:bg-[#003033]">
                <span class="h-2 w-2 rounded-full bg-fuchsia-500"></span>
                <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            </div>

            {{-- Tabel peringkat: 4 kolom (peringkat | nama | skor | waktu) --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] table-fixed border-collapse">
                    <thead>
                        <tr class="h-[38px] border-b border-[#384147] bg-[#1b1d1f] light:border-[#384147] light:bg-[#ECECEC]">
                            <th class="w-[18%] pl-6 pr-2 py-3 text-right text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Peringkat</th>
                            <th class="w-[28%] px-3 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Nama Pengguna</th>
                            <th class="w-[22%] px-3 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Skor</th>
                            <th class="w-[18%] px-3 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Waktu</th>
                            @if($isAdmin)<th class="w-[14%] px-3 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Aksi</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ranked as $row)
                            @php
                                $style = $rankStyle($row->rank, $row->is_me);
                                $minutes = $row->duration_seconds !== null
                                    ? number_format($row->duration_seconds / 60, 2)
                                    : '-';
                            @endphp
                            <tr class="border-b border-[#20272a] transition {{ $style['row'] }} light:border-gray-200">
                                {{-- KOLOM 1: bintang/YOU + nomor peringkat — rata kanan, padding kanan --}}
                                <td class="py-3 pl-6 pr-2">
                                    <div class="flex items-center justify-center">
                                        {{-- Slot bintang/YOU: lebar tetap agar nomor selalu sejajar --}}
                                        <span class="flex w-[70px] shrink-0 items-center justify-end gap-1.5 text-right">
                                            @if($row->is_me)
                                                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-gray-300 light:text-[#003033]">YOU</span>
                                            @endif
                                            @if($style['stars'] > 0)
                                                <span class="text-[15px] leading-none tracking-[0.16em] {{ $style['num'] }}">@for($s = 1; $s <= $style['stars']; $s++)&#9733;@endfor</span>
                                            @endif
                                        </span>
                                        {{-- Nomor peringkat: posisi tetap --}}
                                        <span class="ml-3 w-[24px] shrink-0 text-right text-[13px] font-bold {{ $style['num'] }}">{{ str_pad($row->rank, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>

                                {{-- KOLOM 2: nama pengguna (avatar + nama) — terpusat --}}
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="relative flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-md border border-[#3b494b] bg-[#161822] text-[9px] font-bold text-gray-300 light:border-[#9ca39c] light:bg-[#ECECEC] light:text-[#003033]">
                                            {{ strtoupper(substr($row->user_name, 0, 1)) }}
                                            @if($row->user_image_url)
                                                <img src="{{ $row->user_image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover" onerror="this.remove()">
                                            @endif
                                        </span>
                                        <span class="truncate text-[11px] font-bold uppercase tracking-[0.06em] {{ $style['score'] }}" title="{{ $row->user_name }}">{{ $row->user_name }}</span>
                                    </div>
                                </td>

                                {{-- KOLOM 3: skor + bar proporsi — terpusat --}}
                                <td class="px-3 py-3">
                                    <div class="mx-auto w-[140px] text-center">
                                        <p class="text-[13px] font-bold {{ $style['score'] }}">{{ number_format($row->total_score, 2) }}</p>
                                        <div class="mt-1.5 h-[2px] w-full bg-[#2a2d31] light:bg-[#d1d5db]">
                                            <div class="h-full {{ $style['bar'] }}" style="width: {{ $row->score_percent }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 py-3 text-center">
                                    <span class="text-[13px] font-bold {{ $style['time'] }}">{{ $minutes }}</span>
                                    <span class="ml-1 text-[9px] text-gray-500 light:text-gray-600">min</span>
                                </td>
                                @if($isAdmin)
                                <td class="px-3 py-3 text-center">
                                    @if($row->attempt_id)
                                        <a href="{{ route('attempts.result', $row->attempt_id) }}" aria-label="Lihat hasil {{ $row->user_name }}" class="inline-flex h-6 w-5 items-center justify-center border border-blue-300 bg-[#DBF4FF] text-[#075985]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.5 12s3.5-5.5 9.5-5.5s9.5 5.5 9.5 5.5s-3.5 5.5-9.5 5.5-9.5-5.5-9.5-5.5Z"/><circle cx="12" cy="12" r="2.5" stroke-width="2"/></svg></a>
                                    @else
                                        <span class="text-[10px] text-gray-500">-</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 5 : 4 }}" class="px-6 py-14 text-center text-[11px] text-gray-500 light:text-gray-600">Belum ada peserta yang menyelesaikan challenge ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if(!$isAdmin)
        {{-- Tombol kembali (hanya peserta) --}}
        <div class="mt-4 flex justify-center">
            <a href="{{ route('dashboard') }}"
               class="flex h-11 w-full max-w-[420px] items-center justify-center border border-[#30A192] bg-[#30A192] text-[11px] font-bold uppercase tracking-[0.14em] text-[#061015] hover:opacity-90 light:border-[#2563eb] light:bg-[#2563eb] light:text-white">KEMBALI KE BERANDA</a>
        </div>
        @endif
    </div>
</x-app-layout>
