<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Activity Log</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">
                Riwayat aktivitas request terakhir beserta detail teknisnya.
            </flux:text>
        </div>

        {{-- Toolbar Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari URL, route, IP, atau user..."
                        icon="magnifying-glass"
                        class="w-full sm:w-80"
                    />
                    <flux:select wire:model.live="filterMethod" class="w-32">
                        <option value="">All Methods</option>
                        @foreach($methods as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model.live="filterStatus" class="w-32">
                        <option value="">All Status</option>
                        <option value="2xx">2xx Success</option>
                        <option value="3xx">3xx Redirect</option>
                        <option value="4xx">4xx Client</option>
                        <option value="5xx">5xx Server</option>
                    </flux:select>
                </div>
                <flux:select wire:model.live="perPage" class="w-28">
                    <option value="15">15 / page</option>
                    <option value="30">30 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </flux:select>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold cursor-pointer whitespace-nowrap" wire:click="sortBy('method')">
                                Method
                                @if($sortField === 'method')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold cursor-pointer whitespace-nowrap" wire:click="sortBy('status_code')">
                                Status
                                @if($sortField === 'status_code')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">URL</th>
                            <th class="px-4 py-3 font-semibold cursor-pointer whitespace-nowrap" wire:click="sortBy('ip_address')">
                                Remote IP
                                @if($sortField === 'ip_address')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">User</th>
                            <th class="px-4 py-3 font-semibold cursor-pointer whitespace-nowrap" wire:click="sortBy('created_at')">
                                Created At
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Times Ago</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($logs as $log)
                            <tr wire:key="activity-{{ $log->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <flux:badge size="sm" :color="$log->methodColor()">{{ $log->method }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <flux:badge size="sm" :color="$log->statusColor()">{{ $log->status_code }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 max-w-md">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate" title="{{ $log->url }}">
                                        {{ Str::limit($log->url, 70) }}
                                    </div>
                                    @if($log->route_name)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $log->route_name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 whitespace-nowrap font-mono text-xs">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $log->user_name ?? 'Guest' }}
                                    @if($log->guard)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $log->guard }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <flux:button wire:click="view({{ $log->id }})" size="sm" variant="ghost" icon="eye" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $logs->links() }}
        </div>

        {{-- View Modal --}}
        <flux:modal wire:model="showViewModal" name="activity-view-modal" class="md:w-full max-w-3xl">
            <div class="space-y-6">
                <div class="flex flex-wrap items-center gap-3">
                    <flux:heading size="lg">Activity Details</flux:heading>
                    @if($viewingLog)
                        <flux:badge size="sm" :color="$viewingLog->methodColor()">{{ $viewingLog->method }}</flux:badge>
                        <flux:badge size="sm" :color="$viewingLog->statusColor()">{{ $viewingLog->status_code }}</flux:badge>
                    @endif
                </div>

                @if($viewingLog)
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1 sm:col-span-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">URL</span>
                            <p class="text-sm break-all font-mono text-zinc-900 dark:text-zinc-100">{{ $viewingLog->url }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Route</span>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $viewingLog->route_name ?? '—' }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Remote Address</span>
                            <p class="text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $viewingLog->ip_address }}</p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">User</span>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $viewingLog->user_name ?? 'Guest' }}
                                @if($viewingLog->guard)
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $viewingLog->guard }})</span>
                                @endif
                            </p>
                            @if($viewingLog->user_id)
                                <p class="text-xs font-mono text-zinc-500 dark:text-zinc-400">ID: {{ $viewingLog->user_id }}</p>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Duration</span>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $viewingLog->duration_ms !== null ? $viewingLog->duration_ms.' ms' : '—' }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created At</span>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $viewingLog->created_at->format('d M Y H:i:s') }}
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">({{ $viewingLog->created_at->diffForHumans() }})</span>
                            </p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Session</span>
                            <p class="text-xs break-all font-mono text-zinc-900 dark:text-zinc-100">{{ $viewingLog->session_id ?? '—' }}</p>
                        </div>

                        <div class="space-y-1 sm:col-span-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">User Agent</span>
                            <p class="text-xs break-all font-mono rounded-lg bg-zinc-50 p-3 text-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-200">
                                {{ $viewingLog->user_agent ?: '—' }}
                            </p>
                        </div>

                        <div class="space-y-1 sm:col-span-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Referer</span>
                            <p class="text-xs break-all font-mono text-zinc-900 dark:text-zinc-100">{{ $viewingLog->referer ?: '—' }}</p>
                        </div>

                        @if($viewingLog->error)
                            <div class="space-y-1 sm:col-span-2">
                                <span class="text-sm font-medium text-red-600 dark:text-red-400">Error</span>
                                <p class="text-xs break-all font-mono rounded-lg bg-red-50 p-3 text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                    {{ $viewingLog->error }}
                                </p>
                            </div>
                        @endif

                        <div class="space-y-1 sm:col-span-2">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Request Payload</span>
                            @if($viewingLog->payload)
                                <pre class="max-h-64 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-200">{{ json_encode($viewingLog->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Tidak ada payload.</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeViewModal" variant="ghost">Close</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</flux:main>
