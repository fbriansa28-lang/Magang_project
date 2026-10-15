<x-app-layout>
    <x-slot name="header"><div class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#1a2e28]">EDIT MATERI — KELOLA BELAJAR</div></x-slot>
    @can('edit kelola learning')
    <div class="mx-auto max-w-[1180px] pb-12">@if($errors->any())<div class="mb-4 border border-red-500/25 bg-red-500/10 px-3.5 py-2.5 text-[12px] text-red-400 light:bg-red-50 light:text-red-700">@foreach($errors->all() as $error)<span class="block">{{ $error }}</span>@endforeach</div>@endif<form id="learning-form" method="POST" action="{{ route('learning-materials.update',$material) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('managemenlearning.partials.form')</form>
    @if(isset($material->attachments))
    @foreach($material->attachments as $att)
    <form id="del-att-{{ $att->id }}" action="{{ route('learning-materials.attachments.destroy', [$material->id, $att->id]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
    @endif
        @can('edit kelola learning')
        <div id="deleteAttachmentModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16"><div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white"><div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:border-[#003033] light:bg-[#003033]"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS LAMPIRAN</span></div><div class="px-6 py-3"><p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Lampiran yang dihapus tidak dapat dipulihkan.<br>Apakah Anda yakin ingin melanjutkan?</p><div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div><div class="flex justify-end gap-3 pt-3"><button type="button" onclick="closeDeleteAttachmentModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]">BATAL</button><button type="button" onclick="confirmDeleteAttachment()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">HAPUS</button></div></div></div></div>
        <script>let deleteAttachmentId=null;function openDeleteAttachmentModal(id){deleteAttachmentId=id;document.getElementById('deleteAttachmentModal').classList.replace('hidden','flex')}function closeDeleteAttachmentModal(){deleteAttachmentId=null;document.getElementById('deleteAttachmentModal').classList.replace('flex','hidden')}function confirmDeleteAttachment(){if(!deleteAttachmentId)return;document.getElementById('del-att-'+deleteAttachmentId)?.submit()}document.getElementById('deleteAttachmentModal')?.addEventListener('click',e=>{if(e.target===e.currentTarget)closeDeleteAttachmentModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDeleteAttachmentModal()});</script>
        @endcan
    </div>
    @endcan
</x-app-layout>