<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    {{-- Header Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <flux:heading size="xl">Help Desk - Pengaduan</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola tiket pengaduan nasabah</flux:text>
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
            <div class="flex flex-wrap items-center gap-2">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari subjek, nasabah..." 
                    icon="magnifying-glass"
                    class="w-64"
                />
                <flux:select wire:model.live="filterStatus" class="w-40">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Proses</option>
                    <option value="resolved">Selesai</option>
                </flux:select>
            </div>
            <div class="flex items-center gap-2">
                @if(!empty($selected))
                    <flux:button wire:click="confirmBulkDelete" variant="danger" icon="trash">
                        Hapus ({{ count($selected) }})
                    </flux:button>
                @endif
                <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Buat Tiket
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
        <div class="h-full overflow-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-rose-600 to-pink-600 text-white">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('subject')">
                            Subjek
                            @if($sortField === 'subject')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold">Nasabah</th>
                        <th class="px-4 py-3 font-semibold">Agent</th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('status')">
                            Status
                            @if($sortField === 'status')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
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
                    @forelse($complaints as $complaint)
                        <tr wire:key="complaint-{{ $complaint->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="p-4 text-center">
                                <flux:checkbox wire:model.live="selected" value="{{ $complaint->id }}" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $complaint->subject }}</div>
                                @if($complaint->description)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-xs">{{ Str::limit($complaint->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                {{ $complaint->customer?->full_name ?? '-' }}
                                <span class="text-xs text-zinc-500 block">{{ $complaint->customer?->phone_number }}</span>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $complaint->agent?->agent_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <flux:dropdown>
                                    @switch($complaint->status)
                                        @case('pending')
                                            <flux:button size="sm" variant="ghost">
                                                <flux:badge color="zinc">Pending</flux:badge>
                                            </flux:button>
                                            @break
                                        @case('processing')
                                            <flux:button size="sm" variant="ghost">
                                                <flux:badge color="blue">Proses</flux:badge>
                                            </flux:button>
                                            @break
                                        @case('resolved')
                                            <flux:button size="sm" variant="ghost">
                                                <flux:badge color="green">Selesai</flux:badge>
                                            </flux:button>
                                            @break
                                    @endswitch
                                    <flux:menu>
                                        <flux:menu.item wire:click="updateStatus('{{ $complaint->id }}', 'pending')">
                                            <flux:badge color="zinc" class="mr-2">•</flux:badge> Pending
                                        </flux:menu.item>
                                        <flux:menu.item wire:click="updateStatus('{{ $complaint->id }}', 'processing')">
                                            <flux:badge color="blue" class="mr-2">•</flux:badge> Proses
                                        </flux:menu.item>
                                        <flux:menu.item wire:click="updateStatus('{{ $complaint->id }}', 'resolved')">
                                            <flux:badge color="green" class="mr-2">•</flux:badge> Selesai
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                {{ $complaint->created_at->format('d M Y') }}
                                <span class="text-xs block">{{ $complaint->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="openModal('{{ $complaint->id }}')" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button wire:click="confirmDelete('{{ $complaint->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data pengaduan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        {{ $complaints->links() }}
    </div>


    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" name="complaint-modal" class="max-w-2xl">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editId ? 'Edit Pengaduan' : 'Buat Tiket Pengaduan Baru' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:field>
                        <flux:label>Nasabah</flux:label>
                        <flux:select wire:model="customer_id">
                            <option value="">Pilih nasabah...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }} ({{ $customer->nik }})</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="customer_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Agent PIC</flux:label>
                        <flux:select wire:model="agent_id">
                            <option value="">Assign ke agent (opsional)...</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->agent_name }} ({{ $agent->agent_code }})</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="agent_id" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Subjek</flux:label>
                    <flux:input wire:model="subject" placeholder="Masukkan subjek pengaduan" />
                    <flux:error name="subject" />
                </flux:field>

                <flux:field>
                    <flux:label>Deskripsi</flux:label>
                    <flux:textarea wire:model="description" placeholder="Jelaskan detail pengaduan..." rows="4" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        <option value="pending">Pending</option>
                        <option value="processing">Proses</option>
                        <option value="resolved">Selesai</option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editId ? 'Simpan Perubahan' : 'Buat Tiket' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:text>Apakah Anda yakin ingin menghapus pengaduan ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
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
            <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} pengaduan yang dipilih? Tindakan ini tidak dapat dibatalkan.</flux:text>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
    </div>
</flux:main>
