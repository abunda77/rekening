<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Stats Grid --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            {{-- Total Agents --}}
            <div class="relative overflow-hidden rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 dark:border-blue-800 dark:from-blue-950/30 dark:to-indigo-950/30">
                <dt class="truncate text-sm font-medium text-blue-600 dark:text-blue-400">Total Agents</dt>
                <dd class="mt-2 text-3xl font-semibold text-blue-900 dark:text-blue-100">{{ number_format($totalAgents) }}</dd>
                <div class="absolute -right-4 -top-4 -z-10 h-24 w-24 rounded-full bg-blue-500/10 blur-2xl dark:bg-blue-500/20"></div>
            </div>

            {{-- Total Customers --}}
            <div class="relative overflow-hidden rounded-xl border border-purple-200 bg-gradient-to-br from-purple-50 to-fuchsia-50 p-6 dark:border-purple-800 dark:from-purple-950/30 dark:to-fuchsia-950/30">
                <dt class="truncate text-sm font-medium text-purple-600 dark:text-purple-400">Total Customers</dt>
                <dd class="mt-2 text-3xl font-semibold text-purple-900 dark:text-purple-100">{{ number_format($totalCustomers) }}</dd>
                <div class="absolute -right-4 -top-4 -z-10 h-24 w-24 rounded-full bg-purple-500/10 blur-2xl dark:bg-purple-500/20"></div>
            </div>

            {{-- Total Accounts --}}
            <div class="relative overflow-hidden rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-6 dark:border-emerald-800 dark:from-emerald-950/30 dark:to-teal-950/30">
                <dt class="truncate text-sm font-medium text-emerald-600 dark:text-emerald-400">Total Accounts</dt>
                <dd class="mt-2 text-3xl font-semibold text-emerald-900 dark:text-emerald-100">{{ number_format($totalAccounts) }}</dd>
                <div class="absolute -right-4 -top-4 -z-10 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl dark:bg-emerald-500/20"></div>
            </div>

            {{-- Total ATM --}}
            <div class="relative overflow-hidden rounded-xl border border-orange-200 bg-gradient-to-br from-orange-50 to-rose-50 p-6 dark:border-orange-800 dark:from-orange-950/30 dark:to-rose-950/30">
                <dt class="truncate text-sm font-medium text-orange-600 dark:text-orange-400">Total ATM Cards</dt>
                <dd class="mt-2 text-3xl font-semibold text-orange-900 dark:text-orange-100">{{ number_format($totalAtms) }}</dd>
                <div class="absolute -right-4 -top-4 -z-10 h-24 w-24 rounded-full bg-orange-500/10 blur-2xl dark:bg-orange-500/20"></div>
            </div>
        </div>

        {{-- Expiring Accounts Table --}}
        <div class="relative flex h-full flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h3 class="text-lg font-medium text-neutral-900 dark:text-white">Accounts Expiring This Month</h3>
            </div>
            
            <div class="flex-1 overflow-auto">
                <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-400">
                    <thead class="bg-neutral-50 border-b border-neutral-200 text-xs uppercase text-neutral-500 dark:bg-neutral-700/50 dark:border-neutral-700 dark:text-neutral-300">
                        <tr>
                            <th scope="col" class="px-6 py-3">Account Number</th>
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3">Agent</th>
                            <th scope="col" class="px-6 py-3">Bank</th>
                            <th scope="col" class="px-6 py-3">Expired On</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($expiringAccounts as $account)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/25">
                                <td class="px-6 py-4 font-medium text-neutral-900 dark:text-white">{{ $account->account_number }}</td>
                                <td class="px-6 py-4">{{ $account->customer->full_name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $account->agent->agent_name ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $account->bank_name }}</td>
                                <td class="px-6 py-4">{{ $account->expired_on?->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium {{ $account->status === 'active' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20' }}">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-neutral-500 dark:text-neutral-400">
                                    No accounts found expiring this month.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($expiringAccounts->hasPages())
                <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    {{ $expiringAccounts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
