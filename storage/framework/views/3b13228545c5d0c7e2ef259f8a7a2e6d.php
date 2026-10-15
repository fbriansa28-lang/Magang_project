<?php
    $qType = old("questions.$index.question_type", $question->question_type ?? 'pilihan_ganda');
    $qImage = $question->image_url ?? null;
?>
<section class="learning-card p-6" data-question>
    <div class="mb-5 flex items-center justify-between"><h2 class="text-[22px] font-bold text-white light:text-[#003033]">BUAT_SIMULASI_SOAL</h2><?php if($index>0): ?><button type="button" onclick="this.closest('[data-question]').remove();learningQuestionCount()" class="text-red-400">×</button><?php endif; ?></div>
    <input type="hidden" name="questions[<?php echo e($index); ?>][id]" value="<?php echo e($question->id ?? ''); ?>">
    <input type="hidden" name="questions[<?php echo e($index); ?>][remove_image]" value="0" data-remove-image>
    <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-white light:text-black">Soal</label>
    <div class="learning-question-row">
        <div class="learning-editor learning-question-editor">
            <div class="flex h-7 items-center gap-2 border-b border-[#3b494b] px-2 text-white light:text-black">
                <button type="button" onclick="learningWrap(this,'**','**')" class="h-5 w-5 text-[11px] font-bold">B</button>
                <button type="button" onclick="learningWrap(this,'*','*')" class="h-5 w-5 text-[11px] italic">I</button>
                <label class="learning-question-upload" title="Upload gambar soal"><input type="file" name="questions[<?php echo e($index); ?>][image]" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" onchange="showLearningQuestionImage(this)"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v14H4V5Z"/><circle cx="9" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 17 4-4 3 3 2-2 5 3"/></svg></label>
                <div class="learning-question-image <?php echo e($qImage ? 'has-file' : ''); ?>" data-question-image><?php if($qImage): ?><span title="<?php echo e(basename($question->image_path)); ?>"><?php echo e(\Illuminate\Support\Str::limit(basename($question->image_path), 16, '...')); ?></span><button type="button" onclick="removeLearningQuestionImage(this)">×</button><?php endif; ?></div>
            </div>
            <textarea required name="questions[<?php echo e($index); ?>][question]" placeholder="Tulis soal disini..." class="w-full resize-y border-0 bg-transparent px-3 py-2 text-[14px] text-slate-100 outline-none light:text-[#1a2e28]"><?php echo e(old("questions.$index.question", $question->question ?? '')); ?></textarea>
        </div>
        <div class="learning-qtype-dropdown">
            <input type="hidden" name="questions[<?php echo e($index); ?>][question_type]" value="<?php echo e($qType); ?>">
            <button type="button" onclick="toggleLearningTypeMenu(this)" class="learning-qtype-trigger"><span class="learning-qtype-icon"><?php echo e($qType === 'uraian' ? '☰' : '◉'); ?></span><svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5"/></svg></button>
            <div class="learning-qtype-menu hidden"><button type="button" onclick="selectLearningType(this,'pilihan_ganda')">Pilihan Ganda</button><button type="button" onclick="selectLearningType(this,'uraian')">Uraian</button></div>
        </div>
    </div>
    <div class="hidden"><select onchange="toggleLearningType(this)" data-type-proxy><option value="pilihan_ganda" <?php if($qType==='pilihan_ganda'): echo 'selected'; endif; ?>>PILIHAN GANDA</option><option value="uraian" <?php if($qType==='uraian'): echo 'selected'; endif; ?>>URAIAN</option></select></div>
    <div class="mt-5 <?php echo e($qType === 'uraian' ? 'hidden' : ''); ?>" data-mc>
        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-white light:text-black">Jawaban</label>
        <div class="space-y-2"><?php $__currentLoopData = ['a','b','c','d','e']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label class="flex h-10 items-center gap-2 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-[#ececec]"><input name="questions[<?php echo e($index); ?>][option_<?php echo e($opt); ?>]" value="<?php echo e(old("questions.$index.option_$opt", $question->{'option_'.$opt} ?? '')); ?>" placeholder="Option <?php echo e($i+1); ?>" class="min-w-0 flex-1 border-0 bg-transparent text-[12px] text-white outline-none light:text-[#1a2e28]"><input type="radio" name="questions[<?php echo e($index); ?>][correct_option]" value="<?php echo e($opt); ?>" <?php if(old("questions.$index.correct_option", $question->correct_option ?? 'a')===$opt): echo 'checked'; endif; ?>></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
    </div>
    <div class="mt-5 <?php echo e($qType === 'uraian' ? '' : 'hidden'); ?>" data-essay>
        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-white light:text-black">Flag / Kunci Jawaban</label>
        <textarea name="questions[<?php echo e($index); ?>][answer_key]" placeholder="Tulis kunci jawaban disini..." class="learning-editor w-full px-3 py-3 text-[13px] text-white outline-none light:text-[#1a2e28]"><?php echo e(old("questions.$index.answer_key", $question->answer_key ?? '')); ?></textarea>
    </div>
    <div class="mt-5">
        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.14em] text-white light:text-black">Keterangan Jawaban / Hint</label>
        <textarea name="questions[<?php echo e($index); ?>][hint]" placeholder="Tulis keterangan jawaban disini..." class="learning-editor w-full px-3 py-3 text-[13px] text-white outline-none light:text-[#1a2e28]"><?php echo e(old("questions.$index.hint", $question->hint ?? '')); ?></textarea>
    </div>
</section>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenlearning/partials/question.blade.php ENDPATH**/ ?>