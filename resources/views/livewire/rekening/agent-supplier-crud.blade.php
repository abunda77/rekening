<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen Agent Supplier</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data agent supplier penyedia rekening</flux:text>
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
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau no. kontak..."
                        icon="magnifying-glass" class="w-full sm:w-72" />
                </div>
                <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Tambah Agent Supplier
                </flux:button>
            </div>
        </div>

        {{-- Table Section --}}
        <div
            class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('name')">
                                Nama Agent Supplier
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('contact_number')">
                                No. Kontak
                                @if($sortField === 'contact_number')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Rekening Bank</th>
                            <th class="px-4 py-3 font-semibold text-center">Rekening PT</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('created_at')">
                                Dibuat
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($suppliers as $supplier)
                            <tr wire:key="agent-supplier-{{ $supplier->id }}"
                                class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $supplier->name }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $supplier->contact_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <flux:badge color="blue" size="sm">{{ $supplier->accounts_count }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <flux:badge color="cyan" size="sm">{{ $supplier->company_accounts_count }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $supplier->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="openModal('{{ $supplier->id }}')" size="sm"
                                            variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $supplier->id }}')" size="sm"
                                            variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Belum ada data agent supplier
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $suppliers->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="agent-supplier-modal" class="max-w-lg">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Agent Supplier' : 'Tambah Agent Supplier Baru' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Nama Agent Supplier</flux:label>
                        <flux:input wire:model="name" placeholder="Nama lengkap agent supplier" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>No. Kontak</flux:label>
                        <flux:input wire:model="contact_number" placeholder="08xxxxxxxxxx" />
                        <flux:error name="contact_number" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Agent Supplier' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="agent-supplier-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus agent supplier ini? Rekening yang terkait akan kehilangan
                    data agent supplier.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
