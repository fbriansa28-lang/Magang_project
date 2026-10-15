<?php
    // Kartu mengikuti gaya halaman Bank Soal, tetapi nilainya = stok soal
    // kategori yang sedang dipilih pada form (update via JS saat kategori berubah).
    $stats = $stockStats ?? null;

    $statCards = [
        ['label' => 'Total Soal', 'key' => 'total', 'color' => '#c86cff', 'id' => 'stat-total-questions'],
        ['label' => 'Total Soal Mudah', 'key' => 'easy', 'color' => '#30d6a4', 'id' => 'stat-easy'],
        ['label' => 'Total Soal Sedang', 'key' => 'medium', 'color' => '#ffbd19', 'id' => 'stat-medium'],
        ['label' => 'Total Soal Sulit', 'key' => 'hard', 'color' => '#ff3038', 'id' => 'stat-hard'],
    ];
?>


<div class="mb-5 grid grid-cols-4 gap-3">
    <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <section class="h-[105px] min-w-0 border-l-4 bg-[#1b1d1f] px-4 py-3 light:bg-white" style="border-left-color:<?php echo e($stat['color']); ?>">
            <p class="truncate text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600"><?php echo e($stat['label']); ?></p>
            <p id="<?php echo e($stat['id']); ?>" class="mt-2 text-[38px] leading-none" style="color:<?php echo e($stat['color']); ?>"><?php echo e($stats[$stat['key']]['total'] ?? 0); ?></p>
        </section>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<script>
    // Data stok per kategori dari controller: {id: {total, recent, easy, medium, hard}}
    window.csStockByCategory = <?php echo json_encode($stockByCategory ?? [], 15, 512) ?>;
</script>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenchallenge/challenge/partials/stat-cards.blade.php ENDPATH**/ ?>