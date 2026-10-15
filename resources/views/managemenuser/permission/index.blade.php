<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA IZIN</h2>
    </x-slot>

    @can('view permission')
    <div class="max-w-[1080px] mx-auto font-mono text-[12px]">
        @if($errors->any() && !session('edit_permission_id'))
            <div class="mb-4 flex items-start gap-2 border border-[#ff3038]/60 bg-[#ff3038]/10 px-4 py-3 text-[12px] text-[#ff9a9e]">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 3h.01M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/></svg>
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden bg-[#0d0e12] light:bg-[#d1d1d1] border border-[#263239] light:border-[#3b494b]">
            <div class="flex h-10 items-center gap-2 border-b border-[#384147] bg-[#25272a] px-4 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:bg-[#003033] light:text-white">
                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                <span class="ml-1">KELOLA_IZIN // DAFTAR_IZIN</span>
            </div>

            <div class="grid grid-cols-1 gap-2 border-b border-[#384147] bg-[#161822] p-3 light:bg-[#9CA3AF] md:grid-cols-[1fr_1fr]">
                <form method="GET" action="{{ route('permissions.index') }}" class="relative" id="permissionSearchForm">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#30A192]" aria-label="Cari permission">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg>
                    </button>
                    <input type="search" name="keyword" value="{{ $keyword }}" placeholder="CARI..." autocomplete="off" class="h-11 w-full border border-[#3b494b] bg-[#0f1017] pl-11 pr-3 font-mono text-[10px] uppercase tracking-wide text-gray-200 outline-none placeholder:text-gray-500 focus:border-[#30A192] light:bg-white light:text-[#1a2e28] light:placeholder:text-gray-500" oninput="if (this.value === '' && this.defaultValue !== '') this.form.submit()">
                </form>
                @can('create permission')
                    @include('managemenuser.permission.create')
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]">
                            <th class="w-1/3 px-6 py-3 text-left text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Permission</th>
                            <th class="w-1/3 px-6 py-3 text-left text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Dibuat Pada</th>
                            <th class="px-6 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr class="border-b border-[#20272a] bg-[#0d0e12] text-[10px] hover:bg-white/[0.03] light:border-[#b8b8b8] light:bg-white light:hover:bg-[#f5f5f5]">
                                <td class="px-4 py-2"><span class="inline-flex min-w-[130px] rounded-full bg-[#681b37] px-4 py-0.5 text-[11px] font-bold text-gray-100 light:bg-[#2563eb] light:text-white">{{ $permission->name }}</span></td>
                                <td class="px-4 py-2 text-left text-[11px] text-gray-200 light:text-[#003033]">{{ $permission->created_at?->translatedFormat('d M, Y') ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        @can('edit permission')
                                            <button type="button" onclick="openEditPermissionModal({{ $permission->id }})" class="w-6 h-7 inline-flex items-center justify-center gap-1 border border-green-300 bg-[#DCFCE7] text-[#065f46] light:border-cyan-300 light:bg-[#DBFCFF] light:text-[#00363A] text-[10px] font-semibold uppercase tracking-wide hover:bg-green-100 light:hover:bg-cyan-100 transition" aria-label="Edit permission">
                                                <svg class="w-4 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endcan
                                        @can('delete permission')
                                            <button type="button" data-permission-name="{{ $permission->name }}" onclick="openDeletePermissionModal({{ $permission->id }}, this.dataset.permissionName)" class="w-6 h-7 inline-flex items-center justify-center gap-1 border border-red-300 bg-[#FFDBDB] text-[#3A0500] text-[10px] font-semibold uppercase tracking-wide hover:bg-red-100 transition" aria-label="Hapus permission">
                                                <svg class="w-4 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-14 text-center text-sm text-gray-500">Belum ada permission.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>

        @php
            $pageUrl = fn ($p) => route('permissions.index', array_filter([
                'page' => $p,
                'keyword' => $keyword !== '' ? $keyword : null,
            ]));
        @endphp
        <div class="mt-5 flex items-center justify-between flex-wrap gap-2 px-4 py-3 bg-[#353534] light:bg-[#9CA3AF] border border-[#353534] light:border-[#e5e7eb]">
                <span class="text-[10.5px] text-white light:text-black uppercase tracking-wide">
                    Total_Data: {{ number_format($permissions->total()) }}
                    &nbsp;·&nbsp;
                    Menampilkan: {{ str_pad($permissions->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($permissions->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}
                </span>
                <div class="flex items-center gap-1">
                    <a href="{{ $permissions->onFirstPage() ? '#' : $pageUrl($permissions->currentPage() - 1) }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $permissions->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&lsaquo;</a>
                    @php
                        $start = max(1, min($permissions->currentPage() - 1, $permissions->lastPage() - 2));
                        $end = min($permissions->lastPage(), $start + 2);
                    @endphp
                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $pageUrl($p) }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition {{ $p === $permissions->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</a>
                    @endfor
                    <a href="{{ $permissions->hasMorePages() ? $pageUrl($permissions->currentPage() + 1) : '#' }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $permissions->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&rsaquo;</a>
                </div>
        </div>
    </div>
    @endcan

    @can('edit permission')
        @include('managemenuser.permission.edit')
    @endcan

    @can('delete permission')
    <div id="deletePermissionModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16">
        <div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white">
            <div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:bg-[#003033] light:border-[#003033]">
                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                <span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS IZIN</span>
            </div>
            <div class="px-6 py-3">
                <p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Menghapus permission <span id="deletePermissionName" class="font-bold text-[#ff9a9e]"></span> dapat mempengaruhi akses pengguna.<br>Apakah Anda yakin?</p>
                <div class="flex justify-center py-0.5 text-[#ff3038]">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="confirmDeletePermission()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">HAPUS</button>
                    <button type="button" onclick="closeDeletePermissionModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]">BATAL</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deletePermissionForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endcan

    <script>
        // edit(permission_id) -> ambil detail permission dari DB.
        async function requestFormEditPermission(id) {
            const response = await fetch(`/permissions/${id}/edit`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error('Gagal memuat detail permission');
            }

            return response.json();
        }

        async function openEditPermissionModal(id, name) {
            const modal = document.getElementById('editPermissionModal');
            const form = document.getElementById('editPermissionForm');
            const input = document.getElementById('editPermissionName');
            const errorBox = document.getElementById('editPermissionError');

            if (typeof name !== 'string') {
                // Jalur klik icon edit: ambil detail terbaru dari database.
                try {
                    const data = await requestFormEditPermission(id);
                    name = data.name;
                } catch (error) {
                    alert(error.message);
                    return;
                }

                if (errorBox) {
                    errorBox.classList.add('hidden');
                }
            }

            form.action = `/permissions/${id}`;
            input.value = name;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            input.focus();
        }

        function closeEditPermissionModal() {
            const modal = document.getElementById('editPermissionModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        let deletePermissionId = null;

        // openDeletePermissionModal(permission_id, name) -> tampilkan konfirmasi hapus.
        // Nama permission diambil dari data-attribute baris tabel (tanpa request tambahan).
        function openDeletePermissionModal(id, name) {
            deletePermissionId = id;

            const nameEl = document.getElementById('deletePermissionName');
            if (nameEl) {
                nameEl.textContent = `"${name}"`;
            }

            const modal = document.getElementById('deletePermissionModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeletePermissionModal() {
            deletePermissionId = null;
            const modal = document.getElementById('deletePermissionModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function confirmDeletePermission() {
            if (!deletePermissionId) return;
            const form = document.getElementById('deletePermissionForm');
            form.action = `/permissions/${deletePermissionId}`;
            form.submit();
        }

        document.getElementById('editPermissionModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeEditPermissionModal();
        });

        document.getElementById('deletePermissionModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeDeletePermissionModal();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeEditPermissionModal();
                closeDeletePermissionModal();
            }
        });
    </script>

    @if(session('edit_permission_id'))
        <script>
            // Validasi gagal saat update: buka kembali form edit & tampilkan pesan error.
            openEditPermissionModal({{ (int) session('edit_permission_id') }}, @js(old('name', '')));
        </script>
    @endif
</x-app-layout>
