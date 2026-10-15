<div id="editUserModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16">
    <div class="w-full max-w-[550px] overflow-hidden rounded-md border border-[#14f0e6] bg-[#202020] shadow-[0_0_24px_rgba(20,240,230,0.25)] light:bg-white">
        <div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:border-[#003033] light:bg-[#003033]">
            <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-[#14f0e6] light:text-white">EDIT PENGGUNA</span>
        </div>
        <form id="editUserForm" method="POST" class="px-6 py-5">
            @csrf @method('PUT')
            <input type="hidden" name="role" id="editUserRole">
            <div class="grid grid-cols-[90px_1fr] items-center gap-x-3 gap-y-4 font-mono text-[12px]">
                <span class="text-gray-200 light:text-[#003033]">Nama</span><span id="editUserName" class="text-gray-200 light:text-[#003033]"></span>
                <span class="text-gray-200 light:text-[#003033]">Username</span><span id="editUserUsername" class="text-gray-200 light:text-[#003033]"></span>
                <span class="text-gray-200 light:text-[#003033]">Email</span><span id="editUserEmail" class="text-gray-200 light:text-[#003033]"></span>
                <span class="text-gray-200 light:text-[#003033]">Role</span><div id="editUserRoles" class="flex flex-wrap gap-2"></div>
            </div>
            <div id="editUserError" class="{{ session('edit_user_id') && $errors->any() ? '' : 'hidden' }} mt-4 border border-[#ff3038]/60 bg-[#ff3038]/10 px-3 py-2 font-mono text-[11px] text-[#ff9a9e]">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-6 flex justify-end gap-3"><button type="button" onclick="closeEditUserModal()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">BATAL</button><button type="submit" class="inline-flex items-center gap-1.5 border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN PERUBAHAN</button></div>
        </form>
    </div>
</div>
