<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Rekening PT</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data rekening perusahaan</flux:text>
        </div>

        @if (session('success'))
            <flux:callout variant="success">{{ session('success') }}</flux:callout>
        @endif

        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari PT, rekening, bank, nasabah..."
                        icon="magnifying-glass" class="sm:w-80" />
                    <flux:select wire:model.live="filterStatus" class="sm:w-40">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="bermasalah">Bermasalah</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </flux:select>
                </div>
                <flux:button wire:click="openModal" variant="primary" icon="plus">Tambah Rekening PT</flux:button>
            </div>
        </div>

        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full min-w-5xl text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('company_name')">Nama PT</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('account_number')">No. Rekening</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('bank_name')">Bank</th>
                            <th class="px-4 py-3 font-semibold">Nasabah</th>
                            <th class="px-4 py-3 font-semibold">User</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('opening_date')">Tanggal Buka</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('expired_on')">Tanggal Berakhir</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('status')">Status</th>
                            <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($accounts as $account)
                            <tr wire:key="company-account-{{ $account->id }}" class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $account->company_name }}</td>
                                <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">{{ $account->account_number }}</td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $account->bank_name }}
                                    @if ($account->branch)
                                        <span class="block text-xs text-zinc-500">{{ $account->branch }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->customer?->full_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->agent?->agent_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->opening_date?->format('d M Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->expired_on?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($account->status === 'aktif')
                                        <flux:badge color="green">Aktif</flux:badge>
                                    @elseif ($account->status === 'bermasalah')
                                        <flux:badge color="yellow">Bermasalah</flux:badge>
                                    @else
                                        <flux:badge color="zinc">Non-Aktif</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="view('{{ $account->id }}')" size="sm" variant="ghost" icon="eye" />
                                        <flux:button wire:click="openModal('{{ $account->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $account->id }}')" size="sm" variant="ghost"
                                            icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">Belum ada data rekening PT.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $accounts->links() }}
        </div>

        <flux:modal wire:model="showModal" name="company-account-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Rekening PT' : 'Tambah Rekening PT' }}</flux:heading>
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>Nama PT</flux:label>
                            <flux:input wire:model="company_name" placeholder="PT Contoh Indonesia" />
                            <flux:error name="company_name" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Nasabah</flux:label>
                            <flux:select wire:model="customer_id">
                                <option value="">Tanpa nasabah</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} ({{ $customer->nik }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="customer_id" />
                        </flux:field>
                        <flux:field>
                            <flux:label>User</flux:label>
                            <flux:select wire:model="agent_id">
                                <option value="">Tanpa user</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->agent_name }} ({{ $agent->agent_code }})</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="agent_id" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Nama Bank</flux:label>
                            <flux:select wire:model="bank_name">
                                <option value="">Pilih bank...</option>
                                @foreach ($this->banks as $code => $name)
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
                            <flux:label>Tanggal Berakhir</flux:label>
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
                        <flux:field>
                            <flux:label>Cover Buku Tabungan</flux:label>
                            <flux:input type="file" wire:model="cover_buku" accept="image/*" />
                            <flux:error name="cover_buku" />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:label>Mobile Banking</flux:label>
                        <flux:textarea wire:model="mobile_banking" rows="4" placeholder="User :&#10;Password :&#10;PIN :" />
                        <flux:error name="mobile_banking" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Catatan</flux:label>
                        <flux:textarea wire:model="note" rows="2" placeholder="Catatan tambahan (opsional)" />
                        <flux:error name="note" />
                    </flux:field>
                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button type="button" wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,cover_buku">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Rekening PT' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <flux:modal wire:model="showViewModal" name="company-account-view-modal" class="max-w-3xl">
            <div class="space-y-6">
                <flux:heading size="lg">Detail Rekening PT</flux:heading>
                @if ($viewingAccount)
                    <dl class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div><dt class="text-sm text-zinc-500">Nama PT</dt><dd class="font-medium">{{ $viewingAccount->company_name }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">No. Rekening</dt><dd class="font-mono text-blue-600 dark:text-blue-400">{{ $viewingAccount->account_number }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">Bank & Cabang</dt><dd>{{ $viewingAccount->bank_name }}{{ $viewingAccount->branch ? ' - '.$viewingAccount->branch : '' }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">Status</dt><dd>{{ ucfirst($viewingAccount->status) }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">Nasabah</dt><dd>{{ $viewingAccount->customer?->full_name ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">User</dt><dd>{{ $viewingAccount->agent?->agent_name ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">Tanggal Pembukaan</dt><dd>{{ $viewingAccount->opening_date?->format('d M Y') ?? '-' }}</dd></div>
                        <div><dt class="text-sm text-zinc-500">Tanggal Berakhir</dt><dd>{{ $viewingAccount->expired_on?->format('d M Y') ?? '-' }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm text-zinc-500">Mobile Banking</dt><dd class="whitespace-pre-wrap">{{ $viewingAccount->mobile_banking ?? '-' }}</dd></div>
                        <div class="md:col-span-2"><dt class="text-sm text-zinc-500">Catatan</dt><dd>{{ $viewingAccount->note ?? '-' }}</dd></div>
                        @if ($viewingAccount->cover_buku)
                            <div class="md:col-span-2">
                                <dt class="text-sm text-zinc-500">Cover Buku Tabungan</dt>
                                <dd class="mt-2"><img src="{{ asset('storage/'.$viewingAccount->cover_buku) }}" alt="Cover buku {{ $viewingAccount->company_name }}" class="max-h-64 rounded-lg border border-zinc-200 object-cover dark:border-zinc-700"></dd>
                            </div>
                        @endif
                    </dl>
                @endif
                <div class="flex justify-end"><flux:button wire:click="closeViewModal" variant="ghost">Tutup</flux:button></div>
            </div>
        </flux:modal>

        <flux:modal wire:model="showDeleteModal" name="company-account-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus rekening PT ini?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
