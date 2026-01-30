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
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Help Desk</span>
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
                        <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg">
                            <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Help Desk</h2>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Kelola tiket pengaduan dan bantuan nasabah Anda</p>
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
                                    placeholder="Cari subjek, nasabah..." 
                                    class="w-64 pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors placeholder-slate-400"
                                >
                            </div>
                            <select wire:model.live="filterStatus" class="w-40 py-2 pl-3 pr-8 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Proses</option>
                                <option value="resolved">Selesai</option>
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
                                Buat Tiket
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
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('subject')">
                                        Subjek
                                        @if($sortField === 'subject')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nasabah</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('status')">
                                        Status
                                        @if($sortField === 'status')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider cursor-pointer hover:text-slate-700 dark:hover:text-slate-200" wire:click="sortBy('created_at')">
                                        Dibuat
                                        @if($sortField === 'created_at')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse($complaints as $complaint)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="p-4 text-center">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $complaint->id }}" class="rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500 bg-white dark:bg-slate-700">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-slate-900 dark:text-slate-100">{{ $complaint->subject }}</div>
                                            @if($complaint->description)
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mt-1">{{ Str::limit($complaint->description, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $complaint->customer?->full_name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $complaint->customer?->phone_number }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @switch($complaint->status)
                                                @case('pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Pending</span>
                                                    @break
                                                @case('processing')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Proses</span>
                                                    @break
                                                @case('resolved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Selesai</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $complaint->created_at->format('d M Y') }}
                                            <span class="text-xs block">{{ $complaint->created_at->format('H:i') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button wire:click="openViewModal('{{ $complaint->id }}')" class="text-slate-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" title="Lihat Detail">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="openModal('{{ $complaint->id }}')" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDelete('{{ $complaint->id }}')" class="text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <p class="text-base font-medium">Tidak ada data pengaduan</p>
                                                <p class="text-sm mt-1">Buat tiket baru untuk memulai.</p>
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
                    {{ $complaints->links() }}
                </div>

                {{-- Create/Edit Modal --}}
                <x-modal name="complaint-modal" :show="$showModal" wire:model.live="showModal" focusable maxWidth="2xl">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <h2 class="text-lg font-medium text-slate-900 dark:text-white mb-6">
                            {{ $editId ? 'Edit Pengaduan' : 'Buat Tiket Pengaduan Baru' }}
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

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Subjek</label>
                                <input wire:model="subject" type="text" placeholder="Masukkan subjek pengaduan" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</label>
                                <textarea wire:model="description" rows="4" placeholder="Jelaskan detail pengaduan..." class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                <select wire:model="status" class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Proses</option>
                                    <option value="resolved">Selesai</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button wire:click="closeModal">Batal</x-secondary-button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $editId ? 'Simpan Perubahan' : 'Buat Tiket' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>

                {{-- Delete Confirmation Modal --}}
                <x-modal name="delete-modal" :show="$showDeleteModal" wire:model.live="showDeleteModal" focusable maxWidth="sm">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <h2 class="text-lg font-medium text-slate-900 dark:text-white">Konfirmasi Hapus</h2>
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus pengaduan ini? Tindakan ini tidak dapat dibatalkan.</p>
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
                        <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus {{ count($selected) }} pengaduan yang dipilih? Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button wire:click="cancelBulkDelete">Batal</x-secondary-button>
                            <button wire:click="bulkDelete" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Hapus
                            </button>
                        </div>
                    </div>
                </x-modal>

                {{-- View Detail Modal --}}
                <x-modal name="view-modal" :show="$showViewModal" wire:model.live="showViewModal" focusable maxWidth="lg">
                    <div class="p-6 bg-white dark:bg-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-slate-900 dark:text-white">Detail Pengaduan</h2>
                            <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        @if($viewComplaint)
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">Subjek</label>
                                    <p class="mt-1 text-slate-900 dark:text-slate-100 font-semibold">{{ $viewComplaint->subject }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Nasabah</label>
                                        <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewComplaint->customer?->full_name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">{{ $viewComplaint->customer?->phone_number }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 uppercase">Status</label>
                                        <div class="mt-1">
                                            @switch($viewComplaint->status)
                                                @case('pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">Pending</span>
                                                    @break
                                                @case('processing')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Proses</span>
                                                    @break
                                                @case('resolved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">Selesai</span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">Rekening</label>
                                    <p class="mt-1 text-slate-900 dark:text-slate-100">{{ $viewComplaint->account?->bank_name ?? '-' }} - {{ $viewComplaint->account?->account_number ?? '-' }}</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-500 uppercase">Deskripsi</label>
                                    <div class="mt-1 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-lg text-slate-700 dark:text-slate-300 text-sm whitespace-pre-wrap">{{ $viewComplaint->description ?: 'Tidak ada deskripsi' }}</div>
                                </div>
                                
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700 text-xs text-slate-400 flex justify-between">
                                    <span>Dibuat: {{ $viewComplaint->created_at->format('d M Y H:i') }}</span>
                                    <span>Terakhir update: {{ $viewComplaint->updated_at->format('d M Y H:i') }}</span>
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
