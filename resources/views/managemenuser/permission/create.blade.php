<div class="flex h-11 flex-1 items-center gap-2 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white">
    <form id="createPermissionForm" action="{{ route('permissions.store') }}" method="POST" class="flex min-w-0 flex-1 items-center gap-2">
        @csrf
        <input type="text" name="name" id="newPermissionName" value="{{ old('name') }}" placeholder="TAMBAH_IZIN...." class="h-9 min-w-0 flex-1 border-none bg-[#0f1017] px-3 font-mono text-[10px] uppercase tracking-wide text-gray-200 outline-none placeholder:text-gray-500 focus:border-[#30A192] focus:ring-0 light:bg-white light:text-[#1a2e28] light:placeholder:text-gray-500">
        <button type="submit" class="flex h-8 min-w-[160px] shrink-0 items-center justify-center gap-4 bg-[#30A192] px-3 font-mono text-[10px] font-bold uppercase tracking-wide text-[#061015] hover:opacity-90 light:bg-[#2563eb] light:text-white">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            TAMBAH IZIN
        </button>
    </form>
</div>
