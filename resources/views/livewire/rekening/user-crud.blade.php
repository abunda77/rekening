<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen User</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola pengguna sistem dan role mereka</flux:text>
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
                        placeholder="Cari user..." 
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
                        Tambah User
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
                                Nama
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('email')">
                                Email
                                @if($sortField === 'email')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">Roles</th>
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
                        @forelse($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $user->id }}" />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <flux:badge color="emerald" size="sm">Anda</flux:badge>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <flux:badge 
                                                color="{{ $role->name === 'Super Admin' ? 'red' : ($role->name === 'Admin' ? 'orange' : ($role->name === 'Manager' ? 'blue' : 'zinc')) }}"
                                                size="sm"
                                            >
                                                {{ $role->name }}
                                            </flux:badge>
                                        @endforeach
                                        @if($user->roles->isEmpty())
                                            <flux:text class="text-sm text-zinc-400">-</flux:text>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="openModal('{{ $user->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button 
                                            wire:click="confirmDelete('{{ $user->id }}')" 
                                            size="sm" 
                                            variant="ghost" 
                                            icon="trash" 
                                            class="text-red-500 hover:text-red-700 {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $users->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="user-modal" class="max-w-lg">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit User' : 'Tambah User Baru' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Nama Lengkap</flux:label>
                        <flux:input wire:model="name" placeholder="Nama lengkap user" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="email@example.com" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Password {{ $editId ? '(kosongkan jika tidak diubah)' : '' }}</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="********" />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Roles</flux:label>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400 mb-3">
                            Pilih role untuk user ini
                        </flux:text>
                        
                        <div class="space-y-2 border border-neutral-200 dark:border-neutral-700 rounded-lg p-4">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-2 rounded hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedRoles" 
                                        value="{{ $role->name }}"
                                        class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 w-5 h-5"
                                    />
                                    <div class="flex items-center gap-2">
                                        <flux:badge 
                                            color="{{ $role->name === 'Super Admin' ? 'red' : ($role->name === 'Admin' ? 'orange' : ($role->name === 'Manager' ? 'blue' : 'zinc')) }}"
                                            size="sm"
                                        >
                                            {{ $role->name }}
                                        </flux:badge>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <flux:error name="selectedRoles" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah User' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
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
                <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} user yang dipilih? Tindakan ini tidak dapat dibatalkan.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
