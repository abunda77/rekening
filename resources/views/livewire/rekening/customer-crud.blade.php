<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    {{-- Header Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <flux:heading size="xl">Manajemen Customer</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">Kelola data nasabah/pelanggan</flux:text>
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
                    placeholder="Cari NIK, nama, email..." 
                    icon="magnifying-glass"
                    class="w-72"
                />
            </div>
            <div class="flex items-center gap-2">
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
                <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Tambah Customer
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
        <div class="h-full overflow-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                    <tr>
                        <th class="p-4 w-12 text-center">
                            <flux:checkbox wire:model.live="selectAll" />
                        </th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('nik')">
                            NIK
                            @if($sortField === 'nik')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('full_name')">
                            Nama Lengkap
                            @if($sortField === 'full_name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold">Telepon</th>
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
                    @forelse($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="p-4 text-center">
                                <flux:checkbox wire:model.live="selected" value="{{ $customer->id }}" />
                            </td>
                            <td class="px-4 py-3 font-mono text-emerald-600 dark:text-emerald-400">{{ $customer->nik }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $customer->full_name }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $customer->email ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $customer->phone_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button wire:click="view('{{ $customer->id }}')" size="sm" variant="ghost" icon="eye" />
                                    <flux:button wire:click="openModal('{{ $customer->id }}')" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button wire:click="confirmDelete('{{ $customer->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                Tidak ada data customer
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Section --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        {{ $customers->links() }}
    </div>


    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" name="customer-modal" class="max-w-2xl">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $editId ? 'Edit Customer' : 'Tambah Customer Baru' }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:field>
                        <flux:label>NIK (16 digit)</flux:label>
                        <flux:input wire:model="nik" placeholder="3213096212020011" maxlength="16" />
                        <flux:error name="nik" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Nama Lengkap</flux:label>
                        <flux:input wire:model="full_name" placeholder="Nama sesuai KTP" />
                        <flux:error name="full_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Nama Ibu Kandung</flux:label>
                        <flux:input wire:model="mother_name" placeholder="Nama ibu kandung" />
                        <flux:error name="mother_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="email@example.com" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>No. Telepon</flux:label>
                        <flux:input wire:model="phone_number" placeholder="081234567890" />
                        <flux:error name="phone_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Upload KTP</flux:label>
                        
                        <div class="space-y-3">
                            {{-- Preview Area --}}
                            @if ($upload_ktp)
                                @if (method_exists($upload_ktp, 'isPreviewable') && $upload_ktp->isPreviewable())
                                    <div class="relative overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 w-full h-48">
                                        <img src="{{ $upload_ktp->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview KTP">
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 p-1 text-center text-xs text-white">
                                            Preview Upload Baru
                                        </div>
                                    </div>
                                @else
                                    <div class="flex h-48 w-full items-center justify-center rounded-lg border border-dashed border-zinc-300 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                        <div class="flex flex-col items-center gap-2 text-zinc-400">
                                            <flux:icon name="document" class="h-8 w-8" />
                                            <span class="text-sm italic">File: {{ $upload_ktp->getClientOriginalName() }}</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif ($existing_ktp)
                                <div class="relative overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 w-full h-48">
                                    <img src="{{ Storage::url($existing_ktp) }}" class="h-full w-full object-cover" alt="Existing KTP">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 p-1 text-center text-xs text-white">
                                        File Saat Ini
                                    </div>
                                </div>
                            @endif

                            <div
                                x-data="{ uploading: false, progress: 0 }"
                                x-on:livewire-upload-start="uploading = true"
                                x-on:livewire-upload-finish="uploading = false"
                                x-on:livewire-upload-cancel="uploading = false"
                                x-on:livewire-upload-error="uploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <input type="file" wire:model="upload_ktp" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/20 dark:file:text-emerald-400" accept="image/*" />
                                
                                {{-- Progress Bar --}}
                                <div x-show="uploading" class="mt-2 h-1 w-full rounded-full bg-zinc-200 dark:bg-zinc-700">
                                    <div class="h-1 rounded-full bg-emerald-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                </div>
                            </div>
                        </div>

                        <flux:error name="upload_ktp" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:field>
                        <flux:label>Provinsi</flux:label>
                        <flux:select wire:model.live="province_code" placeholder="Pilih Provinsi">
                            @foreach($provinces as $p)
                                <option value="{{ $p['code'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="province_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Kabupaten/Kota</flux:label>
                        <flux:select wire:model.live="regency_code" placeholder="Pilih Kabupaten/Kota" :disabled="!$province_code">
                            @foreach($regencies as $r)
                                <option value="{{ $r['code'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="regency_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Kecamatan</flux:label>
                        <flux:select wire:model.live="district_code" placeholder="Pilih Kecamatan" :disabled="!$regency_code">
                            @foreach($districts as $d)
                                <option value="{{ $d['code'] }}">{{ $d['name'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="district_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Kelurahan/Desa</flux:label>
                        <flux:select wire:model.live="village_code" placeholder="Pilih Kelurahan/Desa" :disabled="!$district_code">
                            @foreach($villages as $v)
                                <option value="{{ $v['code'] }}">{{ $v['name'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="village_code" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Alamat</flux:label>
                    <flux:textarea wire:model="address" placeholder="Alamat lengkap sesuai KTP" rows="2" />
                    <flux:error name="address" />
                </flux:field>

                <flux:field>
                    <flux:label>Catatan</flux:label>
                    <flux:textarea wire:model="note" placeholder="Catatan tambahan (opsional)" rows="2" />
                    <flux:error name="note" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Customer' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
        <div class="space-y-6">
            <flux:heading size="lg">Konfirmasi Hapus</flux:heading>
            <flux:text>Apakah Anda yakin ingin menghapus customer ini? Semua rekening dan kartu terkait juga akan dihapus.</flux:text>
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
            <flux:text>Apakah Anda yakin ingin menghapus {{ count($selected) }} customer yang dipilih? Semua data terkait (rekening, kartu) juga akan dihapus.</flux:text>
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
                <flux:heading size="lg">Detail Customer</flux:heading>
                <flux:description>Informasi lengkap data customer</flux:description>
            </div>

            @if($viewingCustomer)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">NIK</span>
                        <p class="font-mono text-base font-medium text-emerald-600 dark:text-emerald-400">{{ $viewingCustomer->nik }}</p>
                    </div>
                    
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nama Lengkap</span>
                        <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->full_name }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Email</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->email ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">No. Telepon</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->phone_number ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nama Ibu Kandung</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->mother_name ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Provinsi</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->province ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kabupaten/Kota</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->regency ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kecamatan</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->district ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Kelurahan/Desa</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->village ?? '-' }}</p>
                    </div>



                    <div class="md:col-span-2 space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Alamat</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->address ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Catatan</span>
                        <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingCustomer->note ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2 space-y-1">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Foto KTP</span>
                        <div class="mt-2">
                        @if($viewingCustomer->upload_ktp)
                            <img src="{{ Storage::url($viewingCustomer->upload_ktp) }}" class="h-48 w-auto rounded-lg border border-zinc-200 shadow-sm dark:border-zinc-700" alt="KTP {{ $viewingCustomer->full_name }}">
                        @else
                            <div class="flex h-32 w-full items-center justify-center rounded-lg border border-dashed border-zinc-300 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <span class="italic text-zinc-400">Tidak ada foto KTP</span>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-3 pt-4">
                @if($viewingCustomer)
                    <flux:button wire:click="printDetailPdf('{{ $viewingCustomer->id }}')" variant="outline" icon="printer">
                        Print PDF
                    </flux:button>
                @endif
                <flux:button wire:click="closeViewModal" variant="ghost">Tutup</flux:button>
            </div>
        </div>
    </flux:modal>
    </div>
</flux:main>
