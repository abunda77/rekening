<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen Role</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola peran pengguna dan hak akses</flux:text>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <flux:callout variant="success">
                {{ session('success') }}
            </flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger">
                {{ session('error') }}
            </flux:callout>
        @endif

        {{-- Toolbar Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari role..." 
                        icon="magnifying-glass"
                        class="w-64"
                    />
                </div>
                <div class="flex items-center gap-2">
                    @if(!empty($selected))
                        <flux:button wire:click="confirmBulkDelete" variant="danger" icon="trash">
                            Hapus ({{ count($selected) }})
                        </flux:button>
                    @endif
                    <flux:button wire:click="openModal" variant="primary" icon="plus">
                        Tambah Role
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <tr>
                            <th class="p-4 w-12 text-center">
                                <flux:checkbox wire:model.live="selectAll" />
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('name')">
                                Nama Role
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Jumlah Permission</th>
                            <th class="px-4 py-3 font-semibold text-center">Jumlah User</th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Dibuat
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($roles as $role)
                            <tr wire:key="role-{{ $role->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $role->id }}" />
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    <span class="inline-flex items-center gap-2">
                                        <flux:badge color="{{ $role->name === 'Super Admin' ? 'red' : ($role->name === 'Admin' ? 'orange' : ($role->name === 'Manager' ? 'blue' : 'zinc')) }}">
                                            {{ $role->name }}
                                        </flux:badge>
                                        @if($role->name === 'Super Admin')
                                            <flux:icon name="shield" class="h-4 w-4 text-red-500" />
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">
                                    <flux:badge color="indigo" size="sm">{{ $role->permissions_count ?? 0 }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-center text-zinc-600 dark:text-zinc-400">
                                    <flux:badge color="emerald" size="sm">{{ $role->users_count ?? 0 }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $role->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="openModal('{{ $role->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button 
                                            wire:click="confirmDelete('{{ $role->id }}')" 
                                            size="sm" 
                                            variant="ghost" 
                                            icon="trash" 
                                            class="text-red-500 hover:text-red-700 {{ $role->name === 'Super Admin' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $role->name === 'Super Admin' ? 'disabled' : '' }}
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data role
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $roles->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="role-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Role' : 'Tambah Role Baru' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Nama Role</flux:label>
                        <flux:input wire:model="roleName" placeholder="Contoh: Marketing Manager" />
                        <flux:error name="roleName" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Permissions</flux:label>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">
                            Pilih permission yang akan diassign ke role ini
                        </flux:text>
                        
                        <div class="max-h-80 overflow-y-auto space-y-4 border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                            @foreach($permissionsByModule as $module => $permissions)
                                <div class="space-y-2">
                                    <flux:heading size="sm" class="text-indigo-600 dark:text-indigo-400 capitalize">
                                        {{ $module }}
                                    </flux:heading>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($permissions as $permission)
                                            <label class="flex items-center gap-2 p-2 rounded hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="selectedPermissions" 
                                                    value="{{ $permission->name }}"
                                                    class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800"
                                                />
                                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <flux:separator />
                                @endif
                            @endforeach
                        </div>
                        <flux:error name="selectedPermissions" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Role' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus role ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Bulk Delete Confirmation Modal --}}
        <flux:modal wire:model="showBulkDeleteModal" name="bulk-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus Massal</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} role yang dipilih? Tindakan ini tidak dapat dibatalkan.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
