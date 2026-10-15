<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA PERAN</h2></x-slot>
    @can('edit role')
    <div class="mx-auto max-w-[1000px] font-mono">
        <div class="overflow-hidden border border-[#263239] bg-[#0d0e12] light:border-[#1a2e28] light:bg-[#C9C9C9]">
            <div class="flex h-10 items-center gap-2 border-b border-[#384147] bg-[#25272a] px-4 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:border-[#003033] light:bg-[#003033] light:text-white"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-1">KELOLA_PERAN // EDIT_PERAN</span></div>
            <form method="POST" action="{{ route('roles.update', $role) }}" class="px-8 py-7">
                @csrf @method('PUT')
                <label for="name" class="mb-3 block text-[13px] font-bold uppercase tracking-wide text-gray-200 light:text-[#003033]">Nama Peran</label>
                <input id="name" name="name" value="{{ old('name', $role->name) }}" required class="h-10 w-full border border-[#3b494b] bg-[#0d0e12] px-3 text-[12px] text-gray-200 outline-none focus:border-[#14f0e6] light:bg-white light:text-[#003033]">
                @error('name')<p class="mt-2 text-xs text-rose-400">{{ $message }}</p>@enderror
                <div class="mt-4 flex items-center justify-end gap-2"><div class="relative"><svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-width="2" d="m20 20-4-4"/></svg><input id="permissionSearch" type="search" placeholder="Cari" class="h-8 w-[280px] border border-[#3b494b] bg-[#161822] pl-9 text-xs text-gray-200 outline-none light:bg-white light:text-[#003033]"></div><label class="flex items-center gap-2 text-xs text-gray-200 light:text-[#003033]"><input id="selectAll" type="checkbox" class="h-4 w-4 accent-[#30A192]"> Pilih Semua</label></div>
                <h3 class="mt-4 text-[13px] font-bold uppercase text-gray-200 light:text-[#003033]">Permissions</h3>
                <div class="mt-4 grid grid-cols-1 gap-y-4 md:grid-cols-4">
                    @foreach($permissions as $permission)
                        <label class="permission-option flex items-center gap-3 text-[11px] text-gray-200 light:text-[#003033]" data-name="{{ strtolower($permission->name) }}"><input type="checkbox" name="permission[]" value="{{ $permission->name }}" class="permission-checkbox h-4 w-4 accent-[#30A192]" @checked(in_array($permission->name, old('permission', $hasRoles->toArray())))><span>{{ $permission->name }}</span></label>
                    @endforeach
                </div>
                @error('permission')<p class="mt-4 text-xs text-rose-400">{{ $message }}</p>@enderror
                <div class="mt-8 flex justify-end gap-3"><a href="{{ route('roles.index') }}" class="border border-[#ff5f57] bg-[#4b1115] px-5 py-2 text-[10px] font-bold uppercase text-[#ffd7d7]">BATAL</a><button class="inline-flex items-center gap-1.5 border border-[#4f8cff] bg-[#123b78] px-5 py-2 text-[10px] font-bold uppercase text-[#dbeafe]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN</button></div>
            </form>
        </div>
    </div>
    @endcan
    <script>document.getElementById('permissionSearch')?.addEventListener('input',e=>{const v=e.target.value.toLowerCase();document.querySelectorAll('.permission-option').forEach(x=>x.classList.toggle('hidden',!x.dataset.name.includes(v)))});document.getElementById('selectAll')?.addEventListener('change',e=>document.querySelectorAll('.permission-checkbox').forEach(x=>x.checked=e.target.checked));</script>
</x-app-layout>
