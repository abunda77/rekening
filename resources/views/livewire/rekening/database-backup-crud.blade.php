<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Backup Database</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola backup database sistem</flux:text>
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
                        placeholder="Cari backup..." 
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
                    <flux:button 
                        wire:click="createBackup" 
                        variant="primary" 
                        icon="cloud-arrow-down"
                        :loading="$isCreatingBackup"
                    >
                        Backup Sekarang
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
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('filename')">
                                Nama File
                                @if($sortField === 'filename')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('type')">
                                Tipe
                                @if($sortField === 'type')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('status')">
                                Status
                                @if($sortField === 'status')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('size')">
                                Ukuran
                                @if($sortField === 'size')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Tanggal
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($backups as $backup)
                            <tr wire:key="backup-{{ $backup->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $backup->id }}" />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <flux:icon name="document-text" class="w-5 h-5 text-zinc-400" />
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $backup->filename }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <flux:badge 
                                        color="{{ $backup->type === 'manual' ? 'blue' : 'purple' }}"
                                        size="sm"
                                    >
                                        {{ $backup->type === 'manual' ? 'Manual' : 'Terjadwal' }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3">
                                    <flux:badge 
                                        color="{{ $backup->status === 'success' ? 'green' : 'red' }}"
                                        size="sm"
                                    >
                                        {{ $backup->status === 'success' ? 'Sukses' : 'Gagal' }}
                                    </flux:badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $backup->human_readable_size }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $backup->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($backup->status === 'success' && $backup->file_exists)
                                            <a
                                                href="{{ route('rekening.backups.download', $backup->id) }}"
                                                class="inline-flex items-center justify-center p-2 text-blue-500 hover:text-blue-700 transition-colors"
                                                title="Download"
                                            >
                                                <flux:icon name="arrow-down-tray" class="w-4 h-4" />
                                            </a>
                                        @else
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center p-2 text-zinc-300 cursor-not-allowed"
                                                disabled
                                                title="File tidak tersedia"
                                            >
                                                <flux:icon name="arrow-down-tray" class="w-4 h-4" />
                                            </button>
                                        @endif
                                        <flux:button
                                            wire:click="confirmDelete({{ $backup->id }})"
                                            size="sm"
                                            variant="ghost"
                                            icon="trash"
                                            class="text-red-500 hover:text-red-700"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data backup
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $backups->links() }}
        </div>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus backup ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
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
                <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} backup yang dipilih? Tindakan ini tidak dapat dibatalkan.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
