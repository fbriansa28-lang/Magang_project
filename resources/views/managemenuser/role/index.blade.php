<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA PERAN</h2>
    </x-slot>

    @can('view role')
    <div class="max-w-[1160px] mx-auto font-mono text-[12px]">
        <section class="overflow-hidden border border-[#263239] bg-[#0d0e12] light:border-[#1a2e28] light:bg-[#C9C9C9]">
            <div class="flex h-10 items-center gap-2 border-b border-[#384147] bg-[#25272a] px-4 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:border-[#1a2e28] light:bg-[#003033] light:text-white">
                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                <span class="ml-1">KELOLA_PERAN // DAFTAR_PERAN</span>
            </div>

            <div class="flex items-stretch gap-3 border-b border-[#384147] bg-[#161822] p-3 light:bg-[#9CA3AF]">
                <div class="flex flex-1 items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white">
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg>
                    <input id="roleSearch" type="search" placeholder="CARI..." autocomplete="off" oninput="filterRoles(this.value)" class="h-9 min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] uppercase tracking-wide text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#1a2e28]">
                </div>
                @can('create role')
                    <a href="{{ route('roles.create') }}" class="flex h-11 items-center gap-3 bg-[#30A192] px-5 text-[10px] font-bold uppercase tracking-wide text-[#061015] hover:opacity-90 light:bg-[#2563eb] light:text-white"><span class="text-xl font-light">+</span> TAMBAH PERAN</a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]">
                            <th class="w-[20%] px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Peran</th>
                            <th class="w-[42%] px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Izin</th>
                            <th class="w-[23%] px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Dibuat Pada</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr class="role-row border-b border-[#20272a] bg-[#0d0e12] hover:bg-white/[0.03] light:border-gray-200 light:bg-white light:hover:bg-gray-50" data-name="{{ strtolower($role->name) }}">
                                <td class="px-4 py-2 text-center"><span class="inline-flex min-w-[148px] justify-center rounded-full px-4 py-1 text-[12px] font-bold uppercase text-white {{ strtolower($role->name) === 'admin' ? 'bg-violet-600 light:bg-violet-600' : 'bg-[#2563eb] light:bg-[#2563eb]' }}">{{ $role->name }}</span></td>
                                <td class="px-4 py-2 text-center text-[11px] leading-5 text-gray-300 light:text-[#003033]">{{ $role->permissions->pluck('name')->implode(', ') ?: '-' }}</td>
                                <td class="px-4 py-2 text-center text-[11px] text-gray-200 light:text-[#003033]">{{ $role->created_at?->translatedFormat('d M, Y') ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="mx-auto flex w-[60px] items-center gap-3">
                                        @can('edit role')
                                            <a href="{{ route('roles.edit', $role) }}" class="inline-flex h-7 w-6 items-center justify-center border border-green-300 bg-[#DCFCE7] text-[#065f46] light:border-cyan-300 light:bg-[#DBFCFF] light:text-[#00363A] hover:bg-green-100 light:hover:bg-cyan-100" aria-label="Edit role"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                        @endcan
                                        @can('delete role')
                                            @if(strtolower($role->name) !== 'admin')
                                                <button type="button" onclick="openDeleteRoleModal({{ $role->id }})" class="inline-flex h-7 w-6 items-center justify-center border border-red-300 bg-[#FFDBDB] text-[#3A0500] hover:bg-red-100" aria-label="Hapus role"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 01 1 1v3M4 7h16"/></svg></button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-14 text-center text-sm text-gray-500">Belum ada role.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if($roles->hasPages())
            @php
                $pageUrl = fn ($p) => $roles->url($p);
            @endphp
            <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
                <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                    Total_Data: {{ number_format($roles->total()) }}
                    &nbsp;·&nbsp;
                    Menampilkan: {{ str_pad($roles->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($roles->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}
                </span>
                <div class="flex items-center gap-1">
                    <a href="{{ $roles->onFirstPage() ? '#' : $pageUrl($roles->currentPage() - 1) }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $roles->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&lsaquo;</a>
                    @php
                        $start = max(1, min($roles->currentPage() - 1, $roles->lastPage() - 2));
                        $end = min($roles->lastPage(), $start + 2);
                    @endphp
                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $pageUrl($p) }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition {{ $p === $roles->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</a>
                    @endfor
                    <a href="{{ $roles->hasMorePages() ? $pageUrl($roles->currentPage() + 1) : '#' }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $roles->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&rsaquo;</a>
                </div>
            </div>
        @endif
    </div>
    @endcan

    @can('delete role')
    <div id="deleteRoleModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16">
        <div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white">
            <div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:bg-[#003033] light:border-[#003033]">
                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS PERAN</span>
            </div>
            <div class="px-6 py-3">
                <p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Role yang dihapus tidak dapat digunakan kembali.<br>Apakah Anda yakin ingin melanjutkan?</p>
                <div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div>
                <div class="flex justify-end gap-3 pt-3"><button type="button" onclick="confirmDeleteRole()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">HAPUS</button><button type="button" onclick="closeDeleteRoleModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]">BATAL</button></div>
            </div>
        </div>
    </div>
    <form id="deleteRoleForm" method="POST" class="hidden">@csrf @method('DELETE')</form>
    @endcan

    <script>
        let deleteRoleId = null;

        function openDeleteRoleModal(id) {
            deleteRoleId = id;
            const modal = document.getElementById('deleteRoleModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteRoleModal() {
            deleteRoleId = null;
            const modal = document.getElementById('deleteRoleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function confirmDeleteRole() {
            if (!deleteRoleId) return;
            const form = document.getElementById('deleteRoleForm');
            form.action = `/roles/${deleteRoleId}`;
            form.submit();
        }

        function filterRoles(value) {
            const search = value.toLowerCase().trim();
            document.querySelectorAll('.role-row').forEach((row) => row.classList.toggle('hidden', !row.dataset.name.includes(search)));
        }

        document.getElementById('deleteRoleModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeDeleteRoleModal();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeDeleteRoleModal();
        });
    </script>
</x-app-layout>
