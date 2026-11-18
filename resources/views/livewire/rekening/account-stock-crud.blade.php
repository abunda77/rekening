<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Stok Rekening</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola ketersediaan stok rekening bank</flux:text>
        </div>

        {{-- Flash Message --}}
        @if (session('success'))
            <flux:callout variant="success">{{ session('success') }}</flux:callout>
        @endif

        {{-- Toolbar Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari no. rekening, nasabah, bank, agent, user, catatan..."
                        icon="magnifying-glass" class="sm:w-72" />
                    <flux:select wire:model.live="filterInStock" class="sm:w-40">
                        <option value="">Semua Stok</option>
                        <option value="yes">In Stock</option>
                        <option value="no">Out of Stock</option>
                    </flux:select>
                    <flux:select wire:model.live="filterType" class="sm:w-40">
                        <option value="">Semua Tipe</option>
                        @foreach (\App\Models\AccountStock::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="filterVip" class="sm:w-36">
                        <option value="">Semua VIP</option>
                        <option value="yes">VIP</option>
                        <option value="no">Non VIP</option>
                    </flux:select>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if (! empty($selected))
                        <flux:button wire:click="confirmBulkDelete" variant="danger" icon="trash">
                            Hapus ({{ count($selected) }})
                        </flux:button>
                    @endif
                    <flux:button wire:click="openBulkUploadModal" variant="outline" icon="arrow-up-tray">
                        Bulk Upload
                    </flux:button>
                    <flux:button wire:click="openModal" variant="primary" icon="plus">
                        Tambah Stok
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full min-w-5xl text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="w-12 p-4 text-center">
                                <flux:checkbox wire:model.live="selectAll" />
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('account_number')">
                                No. Rek
                                @if ($sortField === 'account_number')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('bank_name')">
                                Bank
                                @if ($sortField === 'bank_name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('account_type')">
                                Tipe
                                @if ($sortField === 'account_type')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('customer_name')">
                                Nama Nasabah/PT
                                @if ($sortField === 'customer_name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">User</th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('is_in_stock')">
                                In Stock
                                @if ($sortField === 'is_in_stock')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('user')">
                                Agent
                                @if ($sortField === 'user')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="cursor-pointer px-4 py-3 font-semibold" wire:click="sortBy('is_vip')">
                                VIP
                                @if ($sortField === 'is_vip')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">Data Image</th>
                            <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($stocks as $stock)
                            <tr wire:key="account-stock-{{ $stock->id }}" class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $stock->id }}" />
                                </td>
                                <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">{{ $stock->account_number }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $stock->bank_name }}</td>
                                <td class="px-4 py-3">
                                    @if ($stock->account_type === \App\Models\AccountStock::TYPE_PT)
                                        <flux:badge color="amber">{{ $stock->typeLabel() }}</flux:badge>
                                    @else
                                        <flux:badge color="blue">{{ $stock->typeLabel() }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">{{ $stock->customer_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $stock->agent?->agent_name ?? '-' }}
                                    @if ($stock->agent)
                                        <span class="block text-xs text-zinc-500 dark:text-zinc-500">{{ $stock->agent->agent_code }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($stock->is_in_stock)
                                        <flux:badge color="green">Yes</flux:badge>
                                    @else
                                        <flux:badge color="zinc">No</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $stock->user ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($stock->is_vip)
                                        <flux:badge color="purple">Yes</flux:badge>
                                    @else
                                        <flux:badge color="zinc">No</flux:badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($stock->image)
                                        <a href="{{ asset('storage/'.$stock->image) }}" target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/'.$stock->image) }}"
                                                alt="Data image {{ $stock->account_number }}"
                                                class="h-10 w-10 rounded-lg border border-zinc-200 object-cover dark:border-zinc-700">
                                        </a>
                                    @else
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="view('{{ $stock->id }}')" size="sm" variant="ghost" icon="eye" />
                                        <flux:button wire:click="openModal('{{ $stock->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $stock->id }}')" size="sm" variant="ghost"
                                            icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">Belum ada data stok rekening.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $stocks->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="account-stock-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Stok Rekening' : 'Tambah Stok Rekening' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <flux:field>
                            <flux:label>No. Rek</flux:label>
                            <flux:input wire:model="account_number" placeholder="1234567890" />
                            <flux:error name="account_number" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Nama Nasabah/PT</flux:label>
                            <flux:input wire:model="customer_name" placeholder="Nama nasabah atau badan/PT" />
                            <flux:error name="customer_name" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Bank</flux:label>
                            <flux:select wire:model="bank_name">
                                <option value="">Pilih bank...</option>
                                @foreach ($this->banks as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="bank_name" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Tipe</flux:label>
                            <flux:select wire:model="account_type">
                                @foreach (\App\Models\AccountStock::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="account_type" />
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
                            <flux:label>Agent</flux:label>
                            <flux:input wire:model="userName" placeholder="Nama / username agent" />
                            <flux:error name="userName" />
                        </flux:field>
                        <flux:field>
                            <flux:label>In Stock</flux:label>
                            <flux:switch wire:model="is_in_stock" label="Rekening tersedia di stok" />
                            <flux:error name="is_in_stock" />
                        </flux:field>
                        <flux:field>
                            <flux:label>VIP</flux:label>
                            <flux:switch wire:model="is_vip" label="Tandai sebagai rekening VIP" />
                            <flux:error name="is_vip" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Catatan</flux:label>
                        <flux:textarea wire:model="note" rows="3" placeholder="Catatan tambahan untuk rekening ini (opsional)" />
                        <flux:error name="note" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Data Image</flux:label>
                        <flux:input type="file" wire:model="image" accept="image/*" />
                        <flux:error name="image" />
                        <flux:description>Gambar pendukung data rekening (maks. 2MB).</flux:description>

                        @php
                            $previewUrl = null;
                            if ($image && method_exists($image, 'temporaryUrl')) {
                                try {
                                    $previewUrl = $image->temporaryUrl();
                                } catch (\Throwable $e) {
                                    $previewUrl = null;
                                }
                            }
                        @endphp

                        @if ($previewUrl)
                            <div class="mt-2">
                                <span class="text-xs text-zinc-500">Preview:</span>
                                <img src="{{ $previewUrl }}" alt="Preview data image"
                                    class="mt-1 h-32 w-auto rounded-lg border border-zinc-200 object-cover dark:border-zinc-700">
                            </div>
                        @elseif ($editId && \App\Models\AccountStock::find($editId)?->image)
                            <div class="mt-2">
                                <span class="text-xs text-zinc-500">Saat ini:</span>
                                <img src="{{ asset('storage/'.\App\Models\AccountStock::find($editId)->image) }}"
                                    alt="Data image saat ini"
                                    class="mt-1 h-32 w-auto rounded-lg border border-zinc-200 object-cover dark:border-zinc-700">
                            </div>
                        @endif
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button type="button" wire:click="closeModal" variant="ghost">Batal</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,image">
                            {{ $editId ? 'Simpan Perubahan' : 'Tambah Stok' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Bulk Upload Modal --}}
        <flux:modal wire:model="showBulkUploadModal" name="account-stock-bulk-upload-modal" class="max-w-2xl">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Bulk Upload Data &amp; Image</flux:heading>
                    <flux:description>Unggah data stok rekening sekaligus gambar pendukungnya.</flux:description>
                </div>

                @if (! empty($bulkSummary))
                    <flux:callout variant="success">
                        <ul class="list-inside list-disc space-y-1 text-sm">
                            @foreach ($bulkSummary as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    </flux:callout>
                @endif

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>File Data (Excel/CSV)</flux:label>
                        <flux:input type="file" wire:model="bulkFile" accept=".xlsx,.xls,.csv" />
                        <flux:error name="bulkFile" />
                        <flux:description>
                            Kolom yang dikenali: No. Rek, Nama Nasabah/PT, Bank, Tipe (Personal/PT), Agent, In Stock (Yes/No), User, VIP (Yes/No).
                            Baris dengan no. rekening yang sudah ada akan diperbarui.
                        </flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>Bulk Image</flux:label>
                        <flux:input type="file" wire:model="bulkImages" accept="image/*" multiple />
                        <flux:error name="bulkImages" />
                        <flux:description>
                            Nama file gambar harus sama dengan no. rekening, contoh: <span class="font-mono">1234567890.jpg</span>.
                        </flux:description>
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3">
                    <flux:button wire:click="closeBulkUploadModal" variant="ghost">Tutup</flux:button>
                    <flux:button wire:click="bulkUpload" variant="primary" wire:loading.attr="disabled" wire:target="bulkUpload,bulkFile,bulkImages">
                        Proses Upload
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- View Modal --}}
        <flux:modal wire:model="showViewModal" name="account-stock-view-modal" class="max-w-3xl">
            <div class="space-y-6">
                <flux:heading size="lg">Detail Stok Rekening</flux:heading>

                @if ($viewingStock)
                    <dl class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">No. Rek</dt>
                            <dd class="font-mono font-medium text-blue-600 dark:text-blue-400">{{ $viewingStock->account_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Nama Nasabah/PT</dt>
                            <dd class="text-zinc-900 dark:text-zinc-100">{{ $viewingStock->customer_name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Bank</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $viewingStock->bank_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Tipe</dt>
                            <dd class="mt-1">
                                @if ($viewingStock->account_type === \App\Models\AccountStock::TYPE_PT)
                                    <flux:badge color="amber">{{ $viewingStock->typeLabel() }}</flux:badge>
                                @else
                                    <flux:badge color="blue">{{ $viewingStock->typeLabel() }}</flux:badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Agent</dt>
                            <dd class="text-zinc-900 dark:text-zinc-100">
                                {{ $viewingStock->agent?->agent_name ?? '-' }}
                                @if ($viewingStock->agent)
                                    <span class="block text-xs text-zinc-500">{{ $viewingStock->agent->agent_code }}</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">In Stock</dt>
                            <dd class="mt-1">
                                @if ($viewingStock->is_in_stock)
                                    <flux:badge color="green">Yes</flux:badge>
                                @else
                                    <flux:badge color="zinc">No</flux:badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">User</dt>
                            <dd class="text-zinc-900 dark:text-zinc-100">{{ $viewingStock->user ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">VIP</dt>
                            <dd class="mt-1">
                                @if ($viewingStock->is_vip)
                                    <flux:badge color="purple">Yes</flux:badge>
                                @else
                                    <flux:badge color="zinc">No</flux:badge>
                                @endif
                            </dd>
                        </div>
                        @if ($viewingStock->note)
                            <div class="md:col-span-2">
                                <dt class="text-sm text-zinc-500 dark:text-zinc-400">Catatan</dt>
                                <dd class="whitespace-pre-line text-zinc-900 dark:text-zinc-100">{{ $viewingStock->note }}</dd>
                            </div>
                        @endif
                        @if ($viewingStock->image)
                            <div class="md:col-span-2">
                                <dt class="text-sm text-zinc-500 dark:text-zinc-400">Data Image</dt>
                                <dd class="mt-2">
                                    <img src="{{ asset('storage/'.$viewingStock->image) }}" alt="Data image {{ $viewingStock->account_number }}"
                                        class="max-h-64 w-auto rounded-lg border border-zinc-200 object-cover dark:border-zinc-700">
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Dibuat Pada</dt>
                            <dd class="text-zinc-900 dark:text-zinc-100">{{ $viewingStock->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-zinc-500 dark:text-zinc-400">Diperbarui Pada</dt>
                            <dd class="text-zinc-900 dark:text-zinc-100">{{ $viewingStock->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                        </div>
                    </dl>
                @endif

                <div class="flex justify-end">
                    <flux:button wire:click="closeViewModal" variant="ghost">Tutup</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="account-stock-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus stok rekening ini?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Bulk Delete Confirmation Modal --}}
        <flux:modal wire:model="showBulkDeleteModal" name="account-stock-bulk-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Konfirmasi Hapus Massal</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} stok rekening yang dipilih?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Batal</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
