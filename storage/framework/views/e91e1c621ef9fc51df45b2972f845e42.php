<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> <h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#003033]">BELAJAR</h2> <?php $__env->endSlot(); ?>

    <?php
        // Pecah konten jadi paragraf, lalu bagi dua kolom seimbang seperti referensi.
        $paragraphs = collect(preg_split('/\n\s*\n/', trim((string) $material->content)))
            ->map(fn ($paragraph) => trim($paragraph))
            ->filter()
            ->values();
        $half = (int) ceil($paragraphs->count() / 2);
        $leftParagraphs = $paragraphs->slice(0, $half);
        $rightParagraphs = $paragraphs->slice($half);

        // Format editor: **teks** => bold, *teks* => italic.
        // Konten di-escape lebih dulu agar aman dari HTML mentah (XSS).
        $renderInline = function (?string $text): string {
            $escaped = e((string) $text);
            $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
            $escaped = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $escaped);

            return $escaped;
        };
    ?>

    <div class="mx-auto flex w-full max-w-[900px] min-w-0 flex-col gap-4 font-mono">
        
        <div class="flex justify-start">
            <a href="<?php echo e(route('learning.index')); ?>" class="inline-flex h-8 items-center gap-2 px-3 text-[9px] font-bold uppercase tracking-[0.14em] text-gray-300 hover:text-[#30A192] light:text-[#003033] light:hover:text-[#30A192]"><span>&#8592;</span> KEMBALI KE DAFTAR MATERI</a>
        </div>

        
        <section class="border border-[#263239] bg-[#161822] light:border-[#9ca3af] light:bg-white">
            <div class="px-8 py-6">
                <h1 class="border-b border-[#2a3035] pb-3 text-[24px] font-extrabold uppercase leading-tight tracking-tight text-fuchsia-400 light:border-[#d1d5db] light:text-fuchsia-600"><?php echo e($material->title); ?></h1>

                <?php if(filled($material->description) && $material->description !== $material->content): ?>
                    <p class="mt-3 text-[11.5px] leading-5 text-gray-400 light:text-[#1a2e28]"><?php echo $renderInline($material->description); ?></p>
                <?php endif; ?>

                <?php if($paragraphs->isNotEmpty()): ?>
                    <div class="mt-4 grid grid-cols-1 gap-x-10 gap-y-4 border border-dashed border-[#3b494b] p-6 light:border-[#9ca3af] lg:grid-cols-2">
                        <div class="flex flex-col gap-4">
                            <?php $__currentLoopData = $leftParagraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="whitespace-pre-line break-words text-[12px] leading-6 text-gray-300 light:text-[#1a2e28]"><?php echo $renderInline($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="flex flex-col gap-4">
                            <?php $__currentLoopData = $rightParagraphs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paragraph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="whitespace-pre-line break-words text-[12px] leading-6 text-gray-300 light:text-[#1a2e28]"><?php echo $renderInline($paragraph); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if($material->attachments->isNotEmpty()): ?>
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-[#2a3035] pt-3 light:border-[#d1d5db]">
                        <?php $__currentLoopData = $material->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($attachment->file_type === 'image'): ?>
                                <button type="button" title="<?php echo e($attachment->original_name); ?>" aria-label="<?php echo e($attachment->original_name); ?>" onclick="openAttachmentImage('<?php echo e($attachment->url); ?>')" class="inline-flex h-8 w-8 cursor-zoom-in items-center justify-center border border-[#3b494b] bg-[#0f1017] hover:border-[#30A192] light:bg-[#ECECEC] light:hover:border-[#30A192]">
                                    <svg class="h-4 w-4 text-[#30A192]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1.5" stroke-width="1.8"/><circle cx="9" cy="10" r="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 17 4-4 3 3 2-2 5 3"/></svg>
                                </button>
                            <?php elseif($attachment->file_type === 'video'): ?>
                                <button type="button" title="<?php echo e($attachment->original_name); ?>" aria-label="<?php echo e($attachment->original_name); ?>" onclick="openAttachmentVideo('<?php echo e($attachment->url); ?>')" class="inline-flex h-8 w-8 items-center justify-center border border-[#3b494b] bg-[#0f1017] hover:border-[#a855f7] light:bg-[#ECECEC] light:hover:border-[#a855f7]">
                                    <svg class="h-4 w-4 text-[#a855f7]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="13" height="12" rx="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16 10 5-3v10l-5-3"/></svg>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo e($attachment->url); ?>" download title="<?php echo e($attachment->original_name); ?>" aria-label="<?php echo e($attachment->original_name); ?>" class="inline-flex h-8 w-8 items-center justify-center border border-[#3b494b] bg-[#0f1017] hover:border-[#30A192] light:bg-[#ECECEC] light:hover:border-[#30A192]">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l4 4v14H7V3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 3v4h4"/></svg>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        
        <?php if($material->questions_count > 0): ?>
            <?php $isCompleted = ($progress->status ?? null) === 'completed'; ?>
            <div class="flex justify-center">
                <?php if($isCompleted): ?>
                    <a href="<?php echo e(route('learning.riwayat', $material)); ?>" class="inline-flex h-10 items-center justify-center rounded bg-[#2563eb] px-10 text-[11px] font-bold uppercase tracking-[0.14em] text-white hover:bg-[#1d4ed8] light:bg-[#2563eb] light:hover:bg-[#1d4ed8]">LIHAT HASIL</a>
                <?php else: ?>
                    <a href="<?php echo e(route('learning.simulation', $material)); ?>" class="inline-flex h-10 items-center justify-center rounded bg-[#2563eb] px-10 text-[11px] font-bold uppercase tracking-[0.14em] text-white hover:bg-[#1d4ed8] light:bg-[#2563eb] light:hover:bg-[#1d4ed8]">MULAI SIMULASI</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div id="attachmentImageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4">
        <div class="flex h-full w-full max-w-[900px] flex-col py-6">
            <div class="flex items-center justify-between pb-3">
                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200">GAMBAR</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="zoomAttachmentImage(-0.25)" class="flex h-7 w-7 items-center justify-center border border-[#4f8cff] text-[13px] font-bold text-[#8fb8ff] hover:bg-[#123b78]/40" aria-label="Perkecil">&#8722;</button>
                    <button type="button" onclick="zoomAttachmentImage(0.25)" class="flex h-7 w-7 items-center justify-center border border-[#4f8cff] text-[13px] font-bold text-[#8fb8ff] hover:bg-[#123b78]/40" aria-label="Perbesar">+</button>
                    <button type="button" onclick="resetZoomAttachmentImage()" id="attachmentZoomLabel" class="flex h-7 items-center justify-center border border-[#4f8cff] px-3 text-[9px] font-bold uppercase tracking-[0.12em] text-[#8fb8ff] hover:bg-[#123b78]/40">100%</button>
                    <button type="button" onclick="closeAttachmentModal()" class="flex h-7 w-7 items-center justify-center border border-red-400/70 text-[13px] font-bold text-red-300 hover:bg-red-500/20" aria-label="Tutup">&times;</button>
                </div>
            </div>
            <div class="flex min-h-0 flex-1 overflow-auto border border-[#3b494b] bg-[#0f1017] p-6">
                <img id="attachmentImageEl" src="" alt="Gambar lampiran" class="m-auto block max-w-none select-none" style="width: 100%;">
            </div>
        </div>
    </div>

    
    <div id="attachmentVideoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4">
        <div class="flex w-full max-w-[900px] flex-col py-6">
            <div class="flex items-center justify-between pb-3">
                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200">VIDEO</span>
                <button type="button" onclick="closeAttachmentModal()" class="flex h-7 w-7 items-center justify-center border border-red-400/70 text-[13px] font-bold text-red-300 hover:bg-red-500/20" aria-label="Tutup">&times;</button>
            </div>
            <div class="border border-[#3b494b] bg-[#0f1017] p-2">
                <video id="attachmentVideoEl" src="" controls controlsList="nodownload noplaybackrate" disablepictureinpicture class="max-h-[70vh] w-full bg-black"></video>
            </div>
        </div>
    </div>

    <script>
        // ===== Popup gambar lampiran (zoom) =====
        let attachmentImageZoom = 1;

        function applyAttachmentImageZoom() {
            const img = document.getElementById('attachmentImageEl');
            const label = document.getElementById('attachmentZoomLabel');
            if (!img) return;

            img.style.width = Math.round(attachmentImageZoom * 100) + '%';
            img.style.maxWidth = 'none';
            if (label) label.textContent = Math.round(attachmentImageZoom * 100) + '%';
        }

        function openAttachmentImage(src) {
            const modal = document.getElementById('attachmentImageModal');
            const img = document.getElementById('attachmentImageEl');
            if (!modal || !img) return;

            img.src = src;
            attachmentImageZoom = 1;
            applyAttachmentImageZoom();
            modal.classList.replace('hidden', 'flex');
        }

        function zoomAttachmentImage(step) {
            attachmentImageZoom = Math.min(4, Math.max(0.25, attachmentImageZoom + step));
            applyAttachmentImageZoom();
        }

        function resetZoomAttachmentImage() {
            attachmentImageZoom = 1;
            applyAttachmentImageZoom();
        }

        // ===== Popup video lampiran (tanpa zoom) =====
        function openAttachmentVideo(src) {
            const modal = document.getElementById('attachmentVideoModal');
            const video = document.getElementById('attachmentVideoEl');
            if (!modal || !video) return;

            video.src = src;
            modal.classList.replace('hidden', 'flex');
            video.play().catch(() => {});
        }

        function closeAttachmentModal() {
            const imageModal = document.getElementById('attachmentImageModal');
            const image = document.getElementById('attachmentImageEl');
            if (imageModal) {
                imageModal.classList.replace('flex', 'hidden');
                if (image) image.src = '';
            }

            const videoModal = document.getElementById('attachmentVideoModal');
            const video = document.getElementById('attachmentVideoEl');
            if (videoModal) {
                videoModal.classList.replace('flex', 'hidden');
                if (video) {
                    video.pause();
                    video.removeAttribute('src');
                    video.load();
                }
            }

            attachmentImageZoom = 1;
        }

        document.getElementById('attachmentImageModal')?.addEventListener('click', (event) => {
            if (event.target === event.currentTarget) closeAttachmentModal();
        });

        document.getElementById('attachmentVideoModal')?.addEventListener('click', (event) => {
            if (event.target === event.currentTarget) closeAttachmentModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeAttachmentModal();
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/learning/show.blade.php ENDPATH**/ ?>