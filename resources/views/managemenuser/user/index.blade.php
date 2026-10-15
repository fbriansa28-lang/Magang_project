<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA PENGGUNA</h2></x-slot>

    @can('view user')
    <div class="max-w-[1110px] mx-auto font-mono text-[12px]">
        @if($errors->any() && !session('edit_user_id'))
            <div class="mb-4 flex items-start gap-2 border border-[#ff3038]/60 bg-[#ff3038]/10 px-4 py-3 text-[12px] text-[#ff9a9e]">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 3h.01M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/></svg>
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden border border-[#263239] bg-[#0d0e12] light:border-[#1a2e28] light:bg-[#C9C9C9]">
            <div class="flex min-h-14 items-center justify-between border-b border-[#384147] bg-[#25272a] px-5 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:border-[#003033] light:bg-[#003033] light:text-white">
                <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-1">KELOLA_PENGGUNA // DAFTAR_PENGGUNA</span></div>
                <select id="roleFilter" onchange="filterUsers()" class="mr-1 h-10 min-w-[110px] whitespace-nowrap border border-[#3b494b] bg-[#0f1017] px-4 text-[10px] uppercase text-gray-200 outline-none light:bg-white light:text-[#003033]"><option value="">PERAN</option>@foreach($roles as $role)<option value="{{ strtolower($role->name) }}">{{ strtoupper($role->name) }}</option>@endforeach</select>
            </div>
            <div class="flex items-center gap-4 border-b border-[#384147] bg-[#161822] px-4 py-5 light:bg-[#9CA3AF]">
                <div class="flex h-11 flex-1 items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white"><svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-width="2" d="m20 20-4-4"/></svg><input id="userSearch" type="search" placeholder="CARI..." oninput="filterUsers()" class="h-9 min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] uppercase text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#003033]"></div>
                <div class="flex h-14 min-w-[150px] shrink-0 flex-col items-center justify-center border border-[#3b494b] bg-[#0f1017] px-1 py-2 light:bg-white"><span class="text-[9px] font-bold uppercase tracking-[0.15em] text-gray-300 light:text-[#003033]">TOTAL_PENGGUNA</span><span class="text-2xl font-bold text-yellow-300 light:text-[#EE00BA]">{{ $users->total() }}</span></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse">
                    <thead><tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]"><th class="px-6 py-3 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]">Nama</th><th class="px-6 py-3 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]">Nama Pengguna</th><th class="px-6 py-3 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]">Peran</th><th class="px-6 py-3 text-left text-[10px] uppercase text-gray-300 light:text-[#003033]">Email</th><th class="px-6 py-3 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]">Dibuat Pada</th><th class="px-6 py-3 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($users as $user)
                            @php $userRole = $user->roles->first()?->name ?? '-'; @endphp
                            <tr class="user-row border-b border-[#20272a] bg-[#0d0e12] hover:bg-white/[0.03] light:border-gray-200 light:bg-white light:hover:bg-gray-50" data-search="{{ strtolower($user->name.' '.$user->username.' '.$user->email) }}" data-role="{{ strtolower($userRole) }}">
                                <td class="px-4 py-2 text-center text-[11px] font-bold text-gray-200 light:text-[#003033]">{{ strtoupper($user->name) }}</td><td class="px-4 py-2 text-center text-[11px] font-bold text-gray-200 light:text-[#003033]">{{ strtoupper($user->username) }}</td>
                                <td class="px-4 py-2 text-center"><span class="inline-flex min-w-[90px] justify-center rounded px-3 py-1 text-[10px] font-bold uppercase text-[#061015] {{ strtolower($userRole) === 'admin' ? 'bg-violet-400' : 'bg-cyan-400' }}">{{ strtoupper($userRole) }}</span></td>
                                <td class="px-4 py-2 text-[11px] text-gray-300 light:text-[#003033]">{{ $user->email }}</td><td class="px-4 py-2 text-center text-[11px] text-gray-200 light:text-[#003033]">{{ $user->created_at?->translatedFormat('d M, Y') ?? '-' }}</td>
                                <td class="px-4 py-2 text-center"><div class="flex justify-center gap-3">@can('edit user')<button type="button" onclick="openEditUserModal({{ $user->id }})" class="inline-flex h-7 w-6 items-center justify-center border border-green-300 bg-[#DCFCE7] text-[#065f46] light:border-cyan-300 light:bg-[#DBFCFF] light:text-[#00363A]"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>@endcan @can('delete user')<button type="button" data-user-name="{{ $user->name }}" onclick="openDeleteUserModal({{ $user->id }}, this.dataset.userName)" class="inline-flex h-7 w-6 items-center justify-center border border-red-300 bg-[#FFDBDB] text-[#3A0500]"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16"/></svg></button>@endcan</div></td>
                            </tr>
                        @empty <tr><td colspan="6" class="px-6 py-14 text-center text-sm text-gray-500">Belum ada pengguna.</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
        </section>
        @if($users->hasPages())
            @php
                $pageUrl = fn ($p) => $users->url($p);
            @endphp
            <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
                <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                    Total_Data: {{ number_format($users->total()) }}
                    &nbsp;·&nbsp;
                    Menampilkan: {{ str_pad($users->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($users->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}
                </span>
                <div class="flex items-center gap-1">
                    <a href="{{ $users->onFirstPage() ? '#' : $pageUrl($users->currentPage() - 1) }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $users->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&lsaquo;</a>
                    @php
                        $start = max(1, min($users->currentPage() - 1, $users->lastPage() - 2));
                        $end = min($users->lastPage(), $start + 2);
                    @endphp
                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $pageUrl($p) }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition {{ $p === $users->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</a>
                    @endfor
                    <a href="{{ $users->hasMorePages() ? $pageUrl($users->currentPage() + 1) : '#' }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $users->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&rsaquo;</a>
                </div>
            </div>
        @endif
    </div>
    @endcan
    @can('edit user')
        @include('managemenuser.user.edit')
    @endcan
    @can('delete user')
    <div id="deleteUserModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16">
        <div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white">
            <div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:border-[#003033] light:bg-[#003033]"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS PENGGUNA</span></div>
            <div class="px-6 py-3"><p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Apakah Anda yakin ingin menghapus pengguna <span id="deleteUserName" class="font-bold text-[#ff9a9e]"></span>? Tindakan ini tidak dapat dibatalkan dan seluruh data yang terkait dengan pengguna akan dihapus secara permanen.</p><div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div><div class="flex justify-end gap-3 pt-3"><button type="button" onclick="confirmDeleteUser()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7]">HAPUS</button><button type="button" onclick="closeDeleteUserModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe]">BATAL</button></div></div>
        </div>
    </div>
    <form id="deleteUserForm" method="POST" class="hidden">@csrf @method('DELETE')</form>
    @endcan
    <script>
        // edit(user_id) -> ambil detail user dari DB.
        async function requestFormEditUser(id) {
            const response = await fetch(`/users/${id}/edit`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                throw new Error('Gagal memuat detail user');
            }

            return response.json();
        }

        let deleteUserId = null;

        // openDeleteUserModal(user_id, name) -> tampilkan konfirmasi hapus.
        // Nama user diambil dari data-attribute baris tabel (tanpa request tambahan).
        function openDeleteUserModal(id, name) {
            deleteUserId = id;

            const nameEl = document.getElementById('deleteUserName');
            if (nameEl) {
                nameEl.textContent = `"${name}"`;
            }

            const modal = document.getElementById('deleteUserModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteUserModal() {
            deleteUserId = null;
            const modal = document.getElementById('deleteUserModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function confirmDeleteUser() {
            if (!deleteUserId) return;
            const form = document.getElementById('deleteUserForm');
            form.action = `/users/${deleteUserId}`;
            form.submit();
        }

        async function openEditUserModal(id, selectedRoleOverride) {
            let data;

            try {
                // Jalur klik icon edit: ambil detail terbaru dari database.
                data = await requestFormEditUser(id);
            } catch (error) {
                alert(error.message);
                return;
            }

            const modal = document.getElementById('editUserModal');
            const form = document.getElementById('editUserForm');
            const roleBox = document.getElementById('editUserRoles');
            const errorBox = document.getElementById('editUserError');

            const selectedRoles = (typeof selectedRoleOverride === 'string' && selectedRoleOverride !== '')
                ? [selectedRoleOverride]
                : data.selected_roles;

            form.action = `/users/${id}`;
            document.getElementById('editUserName').textContent = data.name;
            document.getElementById('editUserUsername').textContent = data.username;
            document.getElementById('editUserEmail').textContent = data.email;
            document.getElementById('editUserRole').value = selectedRoles[0] || '';
            roleBox.innerHTML = data.roles.map((role) => `<button type="button" class="role-choice border px-3 py-1 text-[10px] font-bold uppercase transition ${selectedRoles.includes(role) ? 'bg-[#252525] border-[#30A192] text-white light:bg-[#003033] light:text-white' : 'bg-transparent border-[#3b494b] text-gray-400 light:border-[#1a2e28] light:text-[#003033]'}" data-role="${role}" onclick="selectUserRole(this)">${role}</button>`).join('');

            if (errorBox) {
                errorBox.classList.toggle('hidden', typeof selectedRoleOverride !== 'string');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function selectUserRole(button) {
            document.querySelectorAll('.role-choice').forEach((choice) => choice.classList.remove('bg-[#252525]', 'border-[#30A192]', 'text-white', 'light:bg-[#003033]', 'light:text-white'));
            button.classList.add('bg-[#252525]', 'border-[#30A192]', 'text-white', 'light:bg-[#003033]', 'light:text-white');
            document.getElementById('editUserRole').value = button.dataset.role;
        }

        function closeEditUserModal() {
            const modal = document.getElementById('editUserModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('editUserModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeEditUserModal();
        });

        document.getElementById('deleteUserModal')?.addEventListener('click', function (event) {
            if (event.target === this) closeDeleteUserModal();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeEditUserModal();
                closeDeleteUserModal();
            }
        });

        function filterUsers(){const q=document.getElementById('userSearch').value.toLowerCase().trim(),r=document.getElementById('roleFilter').value;document.querySelectorAll('.user-row').forEach(x=>x.classList.toggle('hidden',!(x.dataset.search.includes(q)&&(!r||x.dataset.role===r))))}
    </script>

    @if(session('edit_user_id'))
        <script>
            // Validasi gagal saat update: buka kembali form edit & tampilkan pesan error.
            openEditUserModal({{ (int) session('edit_user_id') }}, @js(old('role', '')));
        </script>
    @endif
</x-app-layout>
