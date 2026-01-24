
<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    {{-- Header Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <flux:heading size="xl">Manajemen Agent</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data agent referral</flux:text>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <flux:callout variant="success">
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- Toolbar Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari agent..." 
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
                    Tambah Agent
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
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('agent_code')">
                            Kode Agent
                            @if($sortField === 'agent_code')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('agent_name')">
                            Nama Agent
                            @if($sortField === 'agent_name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold">Telegram</th>
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
                    @forelse($agents as $agent)
                        <tr wire:key="agent-{{ $agent->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="p-4 text-center">
                                <flux:checkbox wire:model.live="selected" value="{{ $agent->id }}" />
                            </td>
                            <td class="px-4 py-3 font-mono text-indigo-600 dark:text-indigo-400">{{ $agent->agent_code }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $agent->agent_name }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $agent->usertelegram ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $agent->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="openModal('{{ $agent->id }}')" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button wire:click="confirmDelete('{{ $agent->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data agent
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        {{ $agents->links() }}
    </div>


    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" name="agent-modal" class="max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editId ? 'Edit Agent' : 'Tambah Agent Baru' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Kode Agent</flux:label>
                    <flux:input wire:model="agent_code" placeholder="AG-001" />
                    <flux:error name="agent_code" />
                </flux:field>

                <flux:field>
                    <flux:label>Nama Agent</flux:label>
                    <flux:input wire:model="agent_name" placeholder="Nama lengkap agent" />
                    <flux:error name="agent_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Username Telegram</flux:label>
                    <flux:input wire:model="usertelegram" placeholder="@username" />
                    <flux:error name="usertelegram" />
                </flux:field>

                <flux:field>
                    <flux:label>Password {{ $editId ? '(kosongkan jika tidak diubah)' : '' }}</flux:label>
                    <flux:input wire:model="password" type="password" placeholder="********" />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Agent' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:text>Apakah Anda yakin ingin menghapus agent ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
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
            <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} agent yang dipilih? Tindakan ini tidak dapat dibatalkan.</flux:text>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
    </div>
</flux:main>
