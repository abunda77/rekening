<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen Pengiriman</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data pengiriman rekening ke agent</flux:text>
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
                        placeholder="Cari no. resi, ekspedisi, agent..." 
                        icon="magnifying-glass"
                        class="w-72"
                    />
                    <flux:select wire:model.live="filterStatus" class="w-40">
                        <option value="">Semua Status</option>
                        <option value="SENT">Sent</option>
                        <option value="PROCESS">Process</option>
                        <option value="OTW">On The Way</option>
                    </flux:select>
                </div>
                <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Tambah Pengiriman
                </flux:button>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Tanggal
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">Agent</th>
                            <th class="px-4 py-3 font-semibold">No. Rekening</th>
                            <th class="px-4 py-3 font-semibold">Ekspedisi</th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('status')">
                                Status
                                @if($sortField === 'status')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">No. Resi</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($shipments as $shipment)
                            <tr wire:key="shipment-{{ $shipment->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $shipment->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $shipment->agent?->agent_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">
                                    {{ $shipment->account?->account_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $shipment->expedition }}
                                </td>
                                <td class="px-4 py-3">
                                    @switch($shipment->status)
                                        @case('SENT')
                                            <flux:badge color="blue">Sent</flux:badge>
                                            @break
                                        @case('PROCESS')
                                            <flux:badge color="yellow">Process</flux:badge>
                                            @break
                                        @case('OTW')
                                            <flux:badge color="green">On The Way</flux:badge>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 font-mono text-zinc-500 dark:text-zinc-400">
                                    {{ $shipment->receipt_number ?? '-' }}
                                    @if($shipment->receipt_number)
                                        <flux:button wire:click="trackShipment('{{ $shipment->id }}')" size="xs" variant="primary" class="ml-2">
                                            Lacak
                                        </flux:button>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="view('{{ $shipment->id }}')" size="sm" variant="ghost" icon="eye" />
                                        <flux:button wire:click="openModal('{{ $shipment->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $shipment->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data pengiriman
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $shipments->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="shipment-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Pengiriman' : 'Tambah Pengiriman Baru' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>Agent</flux:label>
                            <flux:select wire:model.live="agent_id">
                                <option value="">Pilih agent...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->agent_name }} ({{ $agent->agent_code }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="agent_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Rekening</flux:label>
                            <flux:select wire:model="account_id">
                                <option value="">Pilih rekening...</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->bank_name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="account_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Tanggal Pengiriman</flux:label>
                            <flux:input wire:model="delivery_date" type="date" />
                            <flux:error name="delivery_date" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Ekspedisi</flux:label>
                            <flux:select wire:model="expedition">
                                <option value="">Pilih ekspedisi...</option>
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier['code'] }}">{{ $courier['name'] }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="expedition" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status">
                                <option value="SENT">Sent</option>
                                <option value="PROCESS">Process</option>
                                <option value="OTW">On The Way</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <flux:field>
                            <flux:label>No. Resi</flux:label>
                            <flux:input wire:model="receipt_number" placeholder="Nomor resi pengiriman" />
                            <flux:error name="receipt_number" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Catatan</flux:label>
                        <flux:textarea wire:model="note" placeholder="Catatan tambahan (opsional)" rows="2" />
                        <flux:error name="note" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Pengiriman' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus pengiriman ini?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Tracking Modal --}}
        <flux:modal wire:model="showTrackingModal" name="tracking-modal" class="max-w-2xl">
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading size="lg">Lacak Pengiriman</flux:heading>
                    <flux:button wire:click="closeTrackingModal" variant="ghost" icon="x-mark" size="sm" />
                </div>

                <div class="space-y-4">
                    @if($isLoadingTracking)
                        <div class="flex justify-center p-8">
                            <flux:icon icon="arrow-path" class="h-8 w-8 animate-spin text-zinc-400" />
                        </div>
                    @elseif(isset($trackingResult['error']))
                        <flux:callout variant="danger">
                            {{ $trackingResult['error'] }}
                        </flux:callout>
                    @elseif($trackingResult)
                        <div class="grid grid-cols-2 gap-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                            <div>
                                <div class="text-sm text-zinc-500">No. Resi</div>
                                <div class="font-mono font-medium">{{ $trackingResult['tracking_number'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-zinc-500">Ekspedisi</div>
                                <div class="font-medium uppercase">{{ $trackingResult['courier_code'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-zinc-500">Status Saat Ini</div>
                                <flux:badge color="blue">{{ $trackingResult['current_status'] }}</flux:badge>
                            </div>
                            <div>
                                <div class="text-sm text-zinc-500">Terakhir Update</div>
                                <div class="font-medium">{{ \Carbon\Carbon::createFromTimestamp($trackingResult['last_updated'])->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="relative pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-6">
                            @foreach($trackingResult['histories'] as $history)
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white bg-blue-500 dark:border-zinc-900"></div>
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $history['status'] }}</span>
                                            <span class="text-xs text-zinc-500">{{ \Carbon\Carbon::createFromTimestamp($history['date'])->format('d M Y H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $history['description'] }}</p>
                                        @if(!empty($history['location']))
                                            <p class="text-xs text-zinc-500 icon-map-pin">{{ $history['location'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-zinc-500">
                            Tidak ada data pelacakan tersedia.
                        </div>
                    @endif
                </div>

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeTrackingModal" variant="primary">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- View Modal --}}
        <flux:modal wire:model="showViewModal" name="view-modal" class="max-w-2xl">
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading size="lg">Detail Pengiriman</flux:heading>
                    <flux:button wire:click="closeViewModal" variant="ghost" icon="x-mark" size="sm" />
                </div>

                @if($viewShipment)
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Agent</div>
                            <div class="font-medium">{{ $viewShipment->agent?->agent_name ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">No. Rekening</div>
                            <div class="font-mono font-medium">{{ $viewShipment->account?->account_number ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Tanggal Pengiriman</div>
                            <div class="font-medium">{{ $viewShipment->delivery_date?->format('d M Y') ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Ekspedisi</div>
                            <div class="font-medium">{{ $viewShipment->expedition }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Status</div>
                            @switch($viewShipment->status)
                                @case('SENT')
                                    <flux:badge color="blue">Sent</flux:badge>
                                    @break
                                @case('PROCESS')
                                    <flux:badge color="yellow">Process</flux:badge>
                                    @break
                                @case('OTW')
                                    <flux:badge color="green">On The Way</flux:badge>
                                    @break
                            @endswitch
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">No. Resi</div>
                            <div class="font-mono font-medium">{{ $viewShipment->receipt_number ?? '-' }}</div>
                        </div>
                        <div class="col-span-2 space-y-1">
                            <div class="text-sm text-zinc-500">Catatan</div>
                            <div class="text-sm">{{ $viewShipment->note ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Tanggal Dibuat</div>
                            <div class="text-sm">{{ $viewShipment->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm text-zinc-500">Terakhir Diperbarui</div>
                            <div class="text-sm">{{ $viewShipment->updated_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeViewModal" variant="primary">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
