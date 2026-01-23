<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    {{-- Header Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <flux:heading size="xl">Manajemen Kartu ATM</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data kartu ATM/Debit</flux:text>
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
                    placeholder="Cari no. kartu, rekening, bank..." 
                    icon="magnifying-glass"
                    class="w-72"
                />
            </div>
            <flux:button wire:click="openModal" variant="primary" icon="plus">
                Tambah Kartu
            </flux:button>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
        <div class="h-full overflow-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-amber-600 to-orange-600 text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('card_number')">
                            No. Kartu
                            @if($sortField === 'card_number')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('card_type')">
                            Tipe
                            @if($sortField === 'card_type')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold">Rekening</th>
                        <th class="px-4 py-3 font-semibold">Nasabah</th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('expiry_date')">
                            Kadaluarsa
                            @if($sortField === 'expiry_date')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($cards as $card)
                        <tr wire:key="card-{{ $card->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-amber-600 dark:text-amber-400">
                                {{ substr($card->card_number, 0, 4) }} **** **** {{ substr($card->card_number, -4) }}
                            </td>
                            <td class="px-4 py-3">
                                <flux:badge color="purple">{{ $card->card_type ?? 'Debit' }}</flux:badge>
                            </td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                {{ $card->account?->bank_name }}
                                <span class="text-xs text-zinc-500 block">{{ $card->account?->account_number }}</span>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                {{ $card->account?->customer?->full_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($card->expiry_date)
                                    @if($card->expiry_date->isPast())
                                        <span class="text-red-500 font-medium">{{ $card->expiry_date->format('m/Y') }} (Expired)</span>
                                    @elseif($card->expiry_date->diffInMonths(now()) <= 3)
                                        <span class="text-yellow-500 font-medium">{{ $card->expiry_date->format('m/Y') }}</span>
                                    @else
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ $card->expiry_date->format('m/Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="openModal('{{ $card->id }}')" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button wire:click="confirmDelete('{{ $card->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data kartu
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        {{ $cards->links() }}
    </div>


    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" name="card-modal" class="max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editId ? 'Edit Kartu' : 'Tambah Kartu Baru' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:field>
                    <flux:label>Rekening</flux:label>
                    <flux:select wire:model="account_id">
                        <option value="">Pilih rekening...</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->customer?->full_name }})
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:error name="account_id" />
                </flux:field>

                <flux:field>
                    <flux:label>No. Kartu</flux:label>
                    <flux:input wire:model="card_number" placeholder="1234567890123456" />
                    <flux:error name="card_number" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>CVV {{ $editId ? '(kosongkan jika tidak diubah)' : '' }}</flux:label>
                        <flux:input wire:model="cvv" type="password" placeholder="***" maxlength="3" />
                        <flux:error name="cvv" />
                    </flux:field>

                    <flux:field>
                        <flux:label>PIN {{ $editId ? '(kosongkan jika tidak diubah)' : '' }}</flux:label>
                        <flux:input wire:model="pin_hash" type="password" placeholder="******" maxlength="6" />
                        <flux:error name="pin_hash" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Tanggal Kadaluarsa</flux:label>
                        <flux:input wire:model="expiry_date" type="date" />
                        <flux:error name="expiry_date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Tipe Kartu</flux:label>
                        <flux:input wire:model="card_type" placeholder="Debit, Visa, Mastercard..." />
                        <flux:error name="card_type" />
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Kartu' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:text>Apakah Anda yakin ingin menghapus kartu ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
    </div>
</flux:main>
