<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen Rekening</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data rekening bank</flux:text>
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
                        placeholder="Cari no. rekening, bank, nasabah..." 
                        icon="magnifying-glass"
                        class="w-72"
                    />
                    <flux:select wire:model.live="filterStatus" class="w-40">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="bermasalah">Bermasalah</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </flux:select>
                </div>
                    <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Tambah Rekening
                </flux:button>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0 justify-end">
                @if(!empty($selected))
                    <flux:button wire:click="confirmBulkDelete" variant="danger" icon="trash">
                        Hapus ({{ count($selected) }})
                    </flux:button>
                @endif
                <flux:button wire:click="exportXlsx" variant="outline" icon="arrow-down-tray">
                    XLSX
                </flux:button>
                <flux:button wire:click="exportPdf" variant="outline" icon="document-text">
                    PDF
                </flux:button>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="p-4 w-12 text-center">
                                <flux:checkbox wire:model.live="selectAll" />
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('account_number')">
                                No. Rekening
                                @if($sortField === 'account_number')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('bank_name')">
                                Bank
                                @if($sortField === 'bank_name')
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
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('opening_date')">
                                Tgl Buka
                                @if($sortField === 'opening_date')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('expired_on')">
                                Tgl Berakhir
                                @if($sortField === 'expired_on')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($accounts as $account)
                            <tr wire:key="account-{{ $account->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $account->id }}" />
                                </td>
                                <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">{{ $account->account_number }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $account->bank_name }}
                                    @if($account->branch)
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ $account->branch }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $account->customer?->full_name ?? '-' }}
                                    <span class="text-xs text-zinc-500 block">{{ $account->customer?->nik }}</span>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->agent?->agent_name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @switch($account->status)
                                        @case('aktif')
                                            <flux:badge color="green">Aktif</flux:badge>
                                            @break
                                        @case('bermasalah')
                                            <flux:badge color="yellow">Bermasalah</flux:badge>
                                            @break
                                        @case('nonaktif')
                                            <flux:badge color="zinc">Non-Aktif</flux:badge>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $account->opening_date?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $account->expired_on?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="view('{{ $account->id }}')" size="sm" variant="ghost" icon="eye" />
                                        <flux:button wire:click="openModal('{{ $account->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $account->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data rekening
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $accounts->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="account-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Rekening' : 'Tambah Rekening Baru' }}</flux:heading>

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
                            <flux:label>Agent Referral</flux:label>
                            <flux:select wire:model="agent_id">
                                <option value="">Pilih agent (opsional)...</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->agent_name }} ({{ $agent->agent_code }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="agent_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Nama Bank</flux:label>
                            <flux:select wire:model="bank_name" placeholder="Pilih Bank">
                                <option value="">Pilih Bank...</option>
                                @foreach($this->banks as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="bank_name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Cabang</flux:label>
                            <flux:input wire:model="branch" placeholder="Nama cabang" />
                            <flux:error name="branch" />
                        </flux:field>

                        <flux:field>
                            <flux:label>No. Rekening</flux:label>
                            <flux:input wire:model="account_number" placeholder="1234567890" />
                            <flux:error name="account_number" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Tanggal Pembukaan</flux:label>
                            <flux:input wire:model="opening_date" type="date" />
                            <flux:error name="opening_date" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Tanggal Berakhir (Kerjasama)</flux:label>
                            <flux:input wire:model="expired_on" type="date" />
                            <flux:error name="expired_on" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select wire:model="status">
                                <option value="aktif">Aktif</option>
                                <option value="bermasalah">Bermasalah</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Mobile Banking</flux:label>
                        <flux:textarea wire:model="mobile_banking" placeholder="User : &#10;Password : &#10;PIN :" rows="4" />
                        <flux:error name="mobile_banking" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Catatan</flux:label>
                        <flux:textarea wire:model="note" placeholder="Catatan tambahan (opsional)" rows="2" />
                        <flux:error name="note" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Cover Buku Tabungan</flux:label>
                        <flux:input type="file" wire:model="cover_buku" accept="image/*" />
                        <flux:error name="cover_buku" />

                        @php
                            $previewUrl = null;
                            if ($cover_buku && method_exists($cover_buku, 'temporaryUrl')) {
                                try {
                                    $previewUrl = $cover_buku->temporaryUrl();
                                } catch (\Throwable $e) {
                                    // Ignore error
                                }
                            }
                        @endphp

                        @if ($previewUrl)
                            <div class="mt-2">
                                <span class="text-xs text-zinc-500">Preview:</span>
                                <img src="{{ $previewUrl }}" class="h-32 w-auto rounded-lg border border-zinc-200 object-cover mt-1 dark:border-zinc-700">
                            </div>
                        @elseif ($editId && \App\Models\Account::find($editId)->cover_buku)
                            <div class="mt-2">
                                <span class="text-xs text-zinc-500">Saat ini:</span>
                                <img src="{{ asset('storage/' . \App\Models\Account::find($editId)->cover_buku) }}" class="h-32 w-auto rounded-lg border border-zinc-200 object-cover mt-1 dark:border-zinc-700">
                            </div>
                        @endif
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Rekening' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus rekening ini? Semua kartu ATM terkait juga akan dihapus.</flux:text>
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
                <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} rekening yang dipilih? Semua kartu ATM terkait juga akan dihapus.</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
        {{-- View Modal --}}
        <flux:modal wire:model="showViewModal" name="view-modal" class="md:w-full max-w-4xl">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Rekening</flux:heading>
                    <flux:description>Informasi lengkap data rekening</flux:description>
                </div>

                @if($viewingAccount)
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No. Rekening</span>
                            <p class="font-mono text-base font-medium text-blue-600 dark:text-blue-400">{{ $viewingAccount->account_number }}</p>
                        </div>
                        
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Bank & Cabang</span>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->bank_name }}</p>
                            @if($viewingAccount->branch)
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $viewingAccount->branch }}</p>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nasabah</span>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->customer?->full_name ?? '-' }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">NIK: {{ $viewingAccount->customer?->nik ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Agent Referral</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->agent?->agent_name ?? '-' }}</p>
                            @if($viewingAccount->agent)
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Kode: {{ $viewingAccount->agent->agent_code }}</p>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</span>
                            <div class="mt-1">
                                @switch($viewingAccount->status)
                                    @case('aktif')
                                        <flux:badge color="green">Aktif</flux:badge>
                                        @break
                                    @case('bermasalah')
                                        <flux:badge color="yellow">Bermasalah</flux:badge>
                                        @break
                                    @case('nonaktif')
                                        <flux:badge color="zinc">Non-Aktif</flux:badge>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tanggal Pembukaan</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->opening_date?->format('d M Y') ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tanggal Berakhir</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->expired_on?->format('d M Y') ?? '-' }}</p>
                            
                            @if($viewingAccount->expired_on)
                                @php
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $expiredDate = \Carbon\Carbon::parse($viewingAccount->expired_on)->startOfDay();
                                    $daysRemaining = (int) floor($today->diffInDays($expiredDate, false));
                                    $isExpired = $daysRemaining < 0;
                                    $absdays = abs($daysRemaining);
                                    
                                    // Determine badge color based on days remaining
                                    if ($isExpired) {
                                        $badgeColor = 'red';
                                        $badgeText = 'Sudah Berakhir ' . $absdays . ' hari yang lalu';
                                    } elseif ($daysRemaining <= 7) {
                                        $badgeColor = 'orange';
                                        $badgeText = 'Berakhir minggu ini (' . $daysRemaining . ' hari lagi)';
                                    } elseif ($daysRemaining <= 30) {
                                        $badgeColor = 'yellow';
                                        $badgeText = 'Berakhir bulan ini (' . $daysRemaining . ' hari lagi)';
                                    } else {
                                        $badgeColor = 'green';
                                        $badgeText = 'Masih ' . $daysRemaining . ' hari lagi';
                                    }
                                @endphp
                                
                                <div class="mt-2">
                                    <flux:badge color="{{ $badgeColor }}">{{ $badgeText }}</flux:badge>
                                </div>
                            @endif
                        </div>

                        <div class="md:col-span-2 space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Mobile Banking</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100 whitespace-pre-wrap">{{ $viewingAccount->mobile_banking ?? '-' }}</p>
                        </div>

                        <div class="md:col-span-2 space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Catatan</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->note ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Dibuat Pada</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Diperbarui Pada</span>
                            <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingAccount->updated_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        @if($viewingAccount->cover_buku)
                            <div class="md:col-span-2 space-y-1">
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Cover Buku Tabungan</span>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $viewingAccount->cover_buku) }}" class="max-h-64 w-auto rounded-lg border border-zinc-200 object-cover dark:border-zinc-700" alt="Cover Buku">
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4">
                    @if($viewingAccount)
                        <flux:button wire:click="printDetailPdf('{{ $viewingAccount->id }}')" variant="outline" icon="printer">
                            Print PDF
                        </flux:button>
                    @endif
                    <flux:button wire:click="closeViewModal" variant="ghost">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
