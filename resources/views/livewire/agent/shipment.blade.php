<div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm sticky top-0 z-30 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Side -->
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                         <a href="{{ route('agent.dashboard') }}" class="h-8 w-8 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-lg flex items-center justify-center shadow-md mr-3 hover:opacity-90 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </a>
                        <div class="flex flex-col">
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white leading-tight">Agent Portal</h1>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengiriman</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('agent.dashboard') }}" class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                        back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
                {{-- Header Section --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Pengiriman</h2>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Kelola pengiriman rekening dan kartu nasabah Anda</p>
                        </div>
                    </div>
                </div>

                {{-- Flash Message --}}
                @if(session('success'))
                    <div class="rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 p-4 flex items-center text-green-700 dark:text-green-300 shadow-sm">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Toolbar Section --}}
                <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input 
                                    wire:model.live.debounce.300ms="search" 
                                    type="text" 
                                    placeholder="Cari resi, ekspedisi, nasabah..." 
                                    class="w-64 pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-slate-400"
                                >
                            </div>
                            <select wire:model.live="filterStatus" class="w-40 py-2 pl-3 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Semua Status</option>
                                <option value="SENT">Terkirim</option>
                                <option value="PROCESS">Proses</option>
                                <option value="OTW">Dalam Perjalanan</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!empty($selected))
                                <button wire:click="confirmBulkDelete" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus ({{ count($selected) }})
                                </button>
                            @endif
                            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Pengiriman
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="flex-1 overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800 shadow-sm">
                    <div class="h-full overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="p-4 w-12 text-center">
                                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-700">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('receipt_number')">
                                        No. Resi
                                        @if($sortField === 'receipt_number')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ekspedisi</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('status')">
                                        Status
                                        @if($sortField === 'status')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('delivery_date')">
                                        Tanggal Kirim
                                        @if($sortField === 'delivery_date')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($shipments as $shipment)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="p-4 text-center">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $shipment->id }}" class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-700">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-slate-900 dark:text-slate-100 font-mono">{{ $shipment->receipt_number }}</span>
                                                @if($shipment->receipt_number)
                                                    <button wire:click="trackShipment('{{ $shipment->id }}')" class="inline-flex items-center px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" title="Lacak Resi">
                                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                        </svg>
                                                        Lacak
                                                    </button>
                                                @endif
                                            </div>
                                            @if($shipment->note)
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mt-1">{{ Str::limit($shipment->note, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $shipment->account?->customer?->full_name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $shipment->account?->bank_name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $shipment->expedition }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @switch($shipment->status)
                                                @case('SENT')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Terkirim</span>
                                                    @break
                                                @case('PROCESS')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Proses</span>
                                                    @break
                                                @case('OTW')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Dalam Perjalanan</span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">{{ $shipment->status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $shipment->delivery_date?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button wire:click="openViewModal('{{ $shipment->id }}')" class="text-slate-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" title="Lihat Detail">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="openModal('{{ $shipment->id }}')" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Edit">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDelete('{{ $shipment->id }}')" class="text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Hapus">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <p class="text-base font-medium">Tidak ada data pengiriman</p>
                                                <p class="text-sm mt-1">Tambah pengiriman baru untuk memulai.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Section --}}
                <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 shadow-sm">
                    {{ $shipments->links() }}
                </div>

                {{-- Create/Edit Modal --}}
                <x-modal name="shipment-modal" :show="$showModal" wire:model.live="showModal" focusable maxWidth="2xl">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-6">
                            {{ $editId ? 'Edit Pengiriman' : 'Tambah Pengiriman Baru' }}
                        </h2>

                        <form wire:submit="save" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Rekening Nasabah</label>
                                <select wire:model="account_id" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Pilih rekening...</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->customer->full_name }} ({{ $account->account_number }}) - {{ $account->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">No. Resi</label>
                                    <input wire:model="receipt_number" type="text" placeholder="Masukkan nomor resi" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @error('receipt_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Ekspedisi</label>
                                    <select wire:model="expedition" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">Pilih kurir...</option>
                                        @foreach($couriers as $courier)
                                            <option value="{{ $courier['code'] }}">{{ $courier['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('expedition') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Kirim</label>
                                    <input wire:model="delivery_date" type="date" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @error('delivery_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="SENT">Terkirim</option>
                                        <option value="PROCESS">Proses</option>
                                        <option value="OTW">Dalam Perjalanan</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan</label>
                                <textarea wire:model="note" rows="3" placeholder="Catatan tambahan..." class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                @error('note') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button wire:click="closeModal">Batal</x-secondary-button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $editId ? 'Simpan Perubahan' : 'Tambah Pengiriman' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>

                {{-- Delete Confirmation Modal --}}
                <x-modal name="delete-modal" :show="$showDeleteModal" wire:model.live="showDeleteModal" focusable maxWidth="sm">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <h2 class="text-lg font-medium text-slate-900 dark:text-white">Konfirmasi Hapus</h2>
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus pengiriman ini? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button wire:click="cancelDelete">Batal</x-secondary-button>
                            <button wire:click="delete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus
                            </button>
                        </div>
                    </div>
                </x-modal>

                {{-- Bulk Delete Confirmation Modal --}}
                <x-modal name="bulk-delete-modal" :show="$showBulkDeleteModal" wire:model.live="showBulkDeleteModal" focusable maxWidth="sm">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <h2 class="text-lg font-medium text-slate-900 dark:text-white">Konfirmasi Hapus Massal</h2>
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus {{ count($selected) }} pengiriman yang dipilih? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button wire:click="cancelBulkDelete">Batal</x-secondary-button>
                            <button wire:click="bulkDelete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus
                            </button>
                        </div>
                    </div>
                </x-modal>

                {{-- Tracking Modal --}}
                <x-modal name="tracking-modal" :show="$showTrackingModal" wire:model.live="showTrackingModal" focusable maxWidth="2xl">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Lacak Pengiriman
                            </h2>
                            <button wire:click="closeTrackingModal" class="text-slate-400 hover:text-slate-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @if($isLoadingTracking)
                            <div class="flex flex-col items-center justify-center py-12">
                                <svg class="animate-spin h-8 w-8 text-indigo-500 mb-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-slate-500 dark:text-slate-400">Memuat data tracking...</p>
                            </div>
                        @elseif($trackingResult)
                            @if(isset($trackingResult['error']))
                                <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4 text-red-700 dark:text-red-300">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $trackingResult['error'] }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-4">
                                    {{-- Summary --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <p class="text-xs text-slate-500 uppercase">No. Resi</p>
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white font-mono">{{ $trackingResult['tracking_number'] ?? '-' }}</p>
                                        </div>
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <p class="text-xs text-slate-500 uppercase">Kurir</p>
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white uppercase">{{ $trackingResult['courier_code'] ?? '-' }}</p>
                                        </div>
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <p class="text-xs text-slate-500 uppercase">Status</p>
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $trackingResult['current_status'] ?? '-' }}</p>
                                        </div>
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                            <p class="text-xs text-slate-500 uppercase">Terakhir Update</p>
                                            <p class="text-lg font-semibold text-slate-900 dark:text-white">
                                                {{ isset($trackingResult['last_updated']) ? \Carbon\Carbon::createFromTimestamp($trackingResult['last_updated'])->format('d M Y H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- History --}}
                                    @if(isset($trackingResult['histories']) && count($trackingResult['histories']) > 0)
                                        <div class="mt-6">
                                            <p class="text-xs text-slate-500 uppercase mb-3">Riwayat Pengiriman</p>
                                            <div class="space-y-4 border-l-2 border-slate-200 dark:border-slate-700 ml-2 pl-4">
                                                @foreach($trackingResult['histories'] as $history)
                                                    <div class="relative">
                                                        <div class="absolute -left-[21px] top-1.5 h-2.5 w-2.5 rounded-full border border-white bg-slate-400 dark:border-slate-900"></div>
                                                        <div class="flex flex-col gap-1">
                                                             <div class="text-xs text-slate-500">
                                                                {{ isset($history['date']) ? \Carbon\Carbon::createFromTimestamp($history['date'])->format('d M Y H:i') : '-' }}
                                                            </div>
                                                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $history['description'] ?? '-' }}</p>
                                                            @if(!empty($history['location']))
                                                                <p class="text-xs text-slate-500">{{ $history['location'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button wire:click="closeTrackingModal">Tutup</x-secondary-button>
                        </div>
                    </div>
                </x-modal>

                {{-- View Detail Modal --}}
                <x-modal name="view-modal" :show="$showViewModal" wire:model.live="showViewModal" focusable maxWidth="lg">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-slate-900 dark:text-white">Detail Pengiriman</h2>
                            <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        @if($viewShipment)
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">No. Resi</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <p class="text-slate-900 dark:text-slate-100 font-semibold font-mono">{{ $viewShipment->receipt_number }}</p>
                                        @if($viewShipment->receipt_number)
                                            <button wire:click="trackShipment('{{ $viewShipment->id }}')" class="inline-flex items-center px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors" title="Lacak Resi">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                </svg>
                                                Lacak
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Nasabah</label>
                                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewShipment->account?->customer?->full_name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">{{ $viewShipment->account?->customer?->phone_number }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Status</label>
                                        <div class="mt-1">
                                            @switch($viewShipment->status)
                                                @case('SENT')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Terkirim</span>
                                                    @break
                                                @case('PROCESS')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Proses</span>
                                                    @break
                                                @case('OTW')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Dalam Perjalanan</span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">{{ $viewShipment->status }}</span>
                                            @endswitch
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Ekspedisi</label>
                                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewShipment->expedition }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Tanggal Kirim</label>
                                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewShipment->delivery_date?->format('d M Y') ?? '-' }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">Rekening</label>
                                    <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewShipment->account?->bank_name ?? '-' }} - {{ $viewShipment->account?->account_number ?? '-' }}</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">Catatan</label>
                                    <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg text-slate-700 dark:text-slate-300 text-sm whitespace-pre-wrap">{{ $viewShipment->note ?: 'Tidak ada catatan' }}</div>
                                </div>
                                
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-400 flex justify-between">
                                    <span>Dibuat: {{ $viewShipment->created_at->format('d M Y H:i') }}</span>
                                    <span>Terakhir update: {{ $viewShipment->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button wire:click="closeViewModal">Tutup</x-secondary-button>
                        </div>
                    </div>
                </x-modal>
            </div>
        </div>
    </main>
</div>
