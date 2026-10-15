@php
    // Kartu mengikuti gaya halaman Bank Soal, tetapi nilainya = stok soal
    // kategori yang sedang dipilih pada form (update via JS saat kategori berubah).
    $stats = $stockStats ?? null;

    $statCards = [
        ['label' => 'Total Soal', 'key' => 'total', 'color' => '#c86cff', 'id' => 'stat-total-questions'],
        ['label' => 'Total Soal Mudah', 'key' => 'easy', 'color' => '#30d6a4', 'id' => 'stat-easy'],
        ['label' => 'Total Soal Sedang', 'key' => 'medium', 'color' => '#ffbd19', 'id' => 'stat-medium'],
        ['label' => 'Total Soal Sulit', 'key' => 'hard', 'color' => '#ff3038', 'id' => 'stat-hard'],
    ];
@endphp

{{-- Stat cards live: nilai mengikuti kategori terpilih (JS di partial form). --}}
<div class="mb-5 grid grid-cols-4 gap-3">
    @foreach($statCards as $stat)
        <section class="h-[105px] min-w-0 border-l-4 bg-[#1b1d1f] px-4 py-3 light:bg-white" style="border-left-color:{{ $stat['color'] }}">
            <p class="truncate text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600">{{ $stat['label'] }}</p>
            <p id="{{ $stat['id'] }}" class="mt-2 text-[38px] leading-none" style="color:{{ $stat['color'] }}">{{ $stats[$stat['key']]['total'] ?? 0 }}</p>
        </section>
    @endforeach
</div>

<script>
    // Data stok per kategori dari controller: {id: {total, recent, easy, medium, hard}}
    window.csStockByCategory = @json($stockByCategory ?? []);
</script>
