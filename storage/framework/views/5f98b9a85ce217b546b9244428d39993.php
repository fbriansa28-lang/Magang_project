<div id="editPermissionModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16">
    <div class="w-full max-w-[470px] overflow-hidden rounded-md border border-[#14f0e6] bg-[#202020] shadow-[0_0_24px_rgba(20,240,230,0.25)] light:bg-white light:border-[#14f0e6]">
        <div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:bg-[#003033] light:border-[#003033]">
            <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
            <span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-[#14f0e6] light:text-white">EDIT IZIN</span>
        </div>

        <form id="editPermissionForm" method="POST" class="px-6 py-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <label for="editPermissionName" class="mb-3 block font-mono text-[13px] text-gray-200 light:text-[#003033]">Nama Izin</label>
            <input id="editPermissionName" name="name" type="text" required class="h-12 w-full border border-[#3b494b] bg-[#202020] px-3 font-mono text-[13px] text-gray-200 outline-none focus:border-[#14f0e6] light:bg-[#f1f1f1] light:text-[#003033]">

            <div id="editPermissionError" class="<?php echo e(session('edit_permission_id') && $errors->any() ? '' : 'hidden'); ?> mt-3 border border-[#ff3038]/60 bg-[#ff3038]/10 px-3 py-2 font-mono text-[11px] text-[#ff9a9e]">
                <ul class="list-inside list-disc">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeEditPermissionModal()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">BATAL</button>
                <button type="submit" class="inline-flex items-center gap-1.5 border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN PERUBAHAN</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenuser/permission/edit.blade.php ENDPATH**/ ?>