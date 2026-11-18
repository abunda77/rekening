<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="xl">Rekening Expired</flux:heading>
                    <flux:text class="text-zinc-500 dark:text-zinc-400">
                        Rekening bank dan rekening PT yang berakhir bulan ini dan bulan depan.
                    </flux:text>
                </div>
                <flux:button variant="primary" icon="document-text" href="{{ $exportUrl }}">
                    Export PDF
                </flux:button>
            </div>
        </div>

        {{-- Totals --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Rekening Bank — Bulan Ini', $totals['bank_this_month'], 'border-blue-200 dark:border-blue-800', 'text-blue-600 dark:text-blue-400'],
                ['Rekening Bank — Bulan Depan', $totals['bank_next_month'], 'border-indigo-200 dark:border-indigo-800', 'text-indigo-600 dark:text-indigo-400'],
                ['Rekening PT — Bulan Ini', $totals['pt_this_month'], 'border-cyan-200 dark:border-cyan-800', 'text-cyan-600 dark:text-cyan-400'],
                ['Rekening PT — Bulan Depan', $totals['pt_next_month'], 'border-teal-200 dark:border-teal-800', 'text-teal-600 dark:text-teal-400'],
            ] as [$label, $total, $border, $color])
                <div class="relative overflow-hidden rounded-xl border {{ $border }} bg-white p-4 dark:bg-neutral-800">
                    <dt class="truncate text-sm font-medium {{ $color }}">{{ $label }}</dt>
                    <dd class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ number_format($total) }}</dd>
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid flex-1 gap-2 sm:grid-cols-2 xl:grid-cols-4">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari no. rekening, bank, nasabah, user, agent supplier, PT..."
                        icon="magnifying-glass"
                    />
                    <flux:select wire:model.live="filterType">
                        <option value="">Semua Jenis</option>
                        @foreach (\App\Services\ExpiredAccountQuery::TYPES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="filterPeriod">
                        @foreach (\App\Services\ExpiredAccountQuery::PERIODS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        @foreach (\App\Services\ExpiredAccountQuery::STATUSES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex items-center gap-2">
                    <flux:select wire:model.live="perPage" class="w-32">
                        @foreach ([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}">{{ $option }} / hal</option>
                        @endforeach
                    </flux:select>
                    <flux:button variant="ghost" icon="arrow-path" wire:click="resetFilters">Reset</flux:button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        @php
            $showType = $filterType === '';
            $showCompany = $filterType !== 'bank';
            $columnCount = 7 + ($showType ? 1 : 0) + ($showCompany ? 1 : 0);
            $sortIndicator = fn (string $field) => $sortField === $field ? ($sortDirection === 'asc' ? '↑' : '↓') : '';
            $sortableHeader = 'cursor-pointer px-4 py-3 font-semibold hover:bg-white/10';
        @endphp

        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full min-w-5xl text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            @if ($showType)
                                <th class="px-4 py-3 font-semibold">Jenis</th>
                            @endif
                            @if ($showCompany)
                                <th class="{{ $sortableHeader }}" wire:click="sortBy('company_name')">Nama PT {{ $sortIndicator('company_name') }}</th>
                            @endif
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('account_number')">No. Rekening {{ $sortIndicator('account_number') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('customer_name')">Nasabah {{ $sortIndicator('customer_name') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('agent_name')">User {{ $sortIndicator('agent_name') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('agent_supplier_name')">Agent Supplier {{ $sortIndicator('agent_supplier_name') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('bank_name')">Bank {{ $sortIndicator('bank_name') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('expired_on')">Tanggal Berakhir {{ $sortIndicator('expired_on') }}</th>
                            <th class="{{ $sortableHeader }}" wire:click="sortBy('status')">Status {{ $sortIndicator('status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($accounts as $account)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/25">
                                @if ($showType)
                                    <td class="px-4 py-3">
                                        @if ($account->type === 'bank')
                                            <flux:badge color="blue" size="sm">Bank</flux:badge>
                                        @else
                                            <flux:badge color="cyan" size="sm">PT</flux:badge>
                                        @endif
                                    </td>
                                @endif
                                @if ($showCompany)
                                    <td class="px-4 py-3 font-medium text-neutral-900 dark:text-white">{{ $account->company_name ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3 font-mono text-neutral-900 dark:text-white">{{ $account->account_number }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->customer_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->agent_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->agent_supplier_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $account->bank_name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $account->expired_on ? \Illuminate\Support\Carbon::parse($account->expired_on)->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-rekening.status-badge :status="$account->status" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $columnCount }}" class="px-4 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada rekening yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-2 border-t border-neutral-200 px-4 py-3 dark:border-neutral-700 sm:flex-row sm:items-center sm:justify-between">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ $accounts->firstItem() ?? 0 }}–{{ $accounts->lastItem() ?? 0 }} dari {{ $accounts->total() }} rekening
                </flux:text>
                {{ $accounts->links() }}
            </div>
        </div>
    </div>
</flux:main>
