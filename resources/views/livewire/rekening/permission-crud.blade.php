<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">Manajemen Permission</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Lihat dan kelola hak akses sistem</flux:text>
        </div>

        {{-- Toolbar Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari permission..." 
                        icon="magnifying-glass"
                        class="w-64"
                    />
                    <flux:select wire:model.live="selectedModule" class="w-40">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex items-center gap-2">
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                        Total: {{ $permissions->total() }} permission
                    </flux:text>
                </div>
            </div>
        </div>

        {{-- Permissions by Module Cards --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($permissionsByModule as $module => $modulePermissions)
                <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="sm" class="capitalize text-indigo-600 dark:text-indigo-400">
                            {{ $module }}
                        </flux:heading>
                        <flux:badge color="indigo" size="sm">{{ count($modulePermissions) }}</flux:badge>
                    </div>
                    <div class="space-y-2">
                        @foreach($modulePermissions as $permission)
                            <div class="flex items-center justify-between rounded-lg bg-zinc-50 p-2 dark:bg-zinc-700/50">
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $permission->name }}</span>
                                @php
                                    $roles = $rolesByPermission[$permission->name] ?? [];
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($roles, 0, 2) as $role)
                                        <flux:badge 
                                            color="{{ $role === 'Super Admin' ? 'red' : ($role === 'Admin' ? 'orange' : ($role === 'Manager' ? 'blue' : 'zinc')) }}"
                                            size="sm"
                                        >
                                            {{ $role }}
                                        </flux:badge>
                                    @endforeach
                                    @if(count($roles) > 2)
                                        <flux:badge color="zinc" size="sm">+{{ count($roles) - 2 }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Detailed Table Section --}}
        <div class="flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 bg-zinc-50 p-4 dark:border-neutral-700 dark:bg-zinc-800/50">
                <flux:heading size="sm">Detail Permission</flux:heading>
            </div>
            <div class="h-full overflow-auto">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('name')">
                                Permission
                                @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold">Modul</th>
                            <th class="px-4 py-3 font-semibold">Roles</th>
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Dibuat
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($permissions as $permission)
                            @php
                                $parts = explode(' ', $permission->name);
                                $action = $parts[0] ?? '';
                                $module = $parts[1] ?? 'other';
                                $roles = $rolesByPermission[$permission->name] ?? [];
                            @endphp
                            <tr wire:key="permission-{{ $permission->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <flux:badge 
                                            color="{{ in_array($action, ['create', 'delete']) ? 'red' : ($action === 'edit' ? 'orange' : 'emerald') }}"
                                            size="sm"
                                        >
                                            {{ $action }}
                                        </flux:badge>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $permission->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="capitalize text-zinc-600 dark:text-zinc-400">{{ $module }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($roles as $role)
                                            <flux:badge 
                                                color="{{ $role === 'Super Admin' ? 'red' : ($role === 'Admin' ? 'orange' : ($role === 'Manager' ? 'blue' : 'zinc')) }}"
                                                size="sm"
                                            >
                                                {{ $role }}
                                            </flux:badge>
                                        @endforeach
                                        @if(empty($roles))
                                            <flux:text class="text-sm text-zinc-400">Tidak ada role</flux:text>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $permission->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Tidak ada data permission
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $permissions->links() }}
        </div>
    </div>
</flux:main>
