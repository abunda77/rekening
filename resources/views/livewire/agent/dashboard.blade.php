<div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm sticky top-0 z-30 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                         <div class="h-8 w-8 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-lg flex items-center justify-center shadow-md mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold text-slate-800 dark:text-white">Agent Portal</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button type="button" @click="darkMode = !darkMode" class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <div class="flex items-center">
                        <span class="text-sm text-slate-500 dark:text-slate-400 mr-4 font-medium hidden sm:block">Welcome, {{ Auth::guard('agent')->user()->agent_name ?? 'Agent' }}</span>
                        <button wire:click="logout" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-red-600 hover:border-red-300 dark:hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                <div class="p-6 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Managed Accounts
                        </h2>
                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold">{{ $accounts->total() }} Records</span>
                    </div>
                    
                    @if($accounts->isEmpty())
                        <div class="text-center py-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                            <div class="bg-white dark:bg-slate-800 p-4 rounded-full shadow-sm inline-block mb-3">
                                <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-2 text-base font-semibold text-slate-900 dark:text-white">No accounts found</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">You are not managing any accounts yet.</p>
                        </div>
                    @else
                        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50/50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Account Details Overview</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                    @foreach($accounts as $account)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                                            <td class="px-6 py-6">
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                                    <!-- Rekening Info -->
                                                    <div class="relative pl-4">
                                                        <div class="absolute left-0 top-1 bottom-1 w-1 bg-blue-500 rounded-full group-hover:scale-y-110 transition-transform"></div>
                                                        <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 flex items-center">
                                                            Rekening
                                                        </h4>
                                                        <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $account->bank_name }}</div>
                                                        <div class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">{{ $account->branch }}</div>
                                                        <div class="text-sm font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 rounded inline-block mb-3">{{ $account->account_number }}</div>
                                                        
                                                        <div class="flex items-center space-x-2">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold capitalize {{ $account->status === 'active' ? 'bg-green-100 text-green-700 ring-1 ring-green-600/20' : 'bg-red-100 text-red-700 ring-1 ring-red-600/20' }}">
                                                                {{ $account->status }}
                                                            </span>
                                                             @if($account->mobile_banking)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-100 text-indigo-700 ring-1 ring-indigo-600/20">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                    </svg>
                                                                    M-Banking
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Customer Info -->
                                                    <div class="relative pl-4 lg:border-l lg:border-slate-100 dark:border-slate-700 group/customer cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg p-2 -ml-2"
                                                        wire:click="showCustomerDetail('{{ $account->customer->id }}')">
                                                        <div class="hidden group-hover/customer:block absolute right-2 top-2 text-blue-400">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </div>
                                                        <div class="lg:hidden absolute left-0 top-1 bottom-1 w-1 bg-cyan-500 rounded-full"></div>
                                                        <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 group-hover/customer:text-blue-500 transition-colors">Customer</h4>
                                                        
                                                        @if($account->customer)
                                                            <div class="flex items-start">
                                                                <div class="h-10 w-10 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-700 dark:text-cyan-300 font-bold text-sm mr-3">
                                                                    {{ substr($account->customer->name ?? $account->customer->full_name, 0, 2) }}
                                                                </div>
                                                                <div>
                                                                    <div class="text-base font-bold text-slate-900 dark:text-white">{{ $account->customer->full_name ?? $account->customer->name }}</div>
                                                                    <div class="mt-1 space-y-1">
                                                                        <div class="text-sm text-slate-600 dark:text-slate-400 flex items-center">
                                                                            <svg class="h-3.5 w-3.5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                            </svg>
                                                                            {{ $account->customer->phone_number ?? $account->customer->phone ?? 'No Phone' }}
                                                                        </div>
                                                                        <div class="text-sm text-slate-600 flex items-center">
                                                                            <svg class="h-3.5 w-3.5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                            </svg>
                                                                            {{ $account->customer->email ?? 'No Email' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="text-sm text-slate-400 italic">No customer linked</span>
                                                        @endif
                                                    </div>

                                                    <!-- Card Info -->
                                                    <div class="relative pl-4 lg:border-l lg:border-slate-100 dark:border-slate-700">
                                                        <div class="lg:hidden absolute left-0 top-1 bottom-1 w-1 bg-purple-500 rounded-full"></div>
                                                        <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 flex items-center justify-between">
                                                            Cards 
                                                            <span class="bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 px-2 py-0.5 rounded-full text-[10px]">{{ $account->cards->count() }}</span>
                                                        </h4>
                                                        
                                                        @if($account->cards->isEmpty())
                                                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-center">
                                                                <span class="text-sm text-slate-500 italic">No cards issued</span>
                                                            </div>
                                                        @else
                                                            <div class="space-y-3">
                                                                @foreach($account->cards as $card)
                                                                    <div class="flex items-center p-2.5 bg-slate-50 dark:bg-slate-700/30 hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm border border-slate-100 dark:border-slate-700 hover:border-slate-200 dark:hover:border-slate-600 rounded-lg transition-all cursor-pointer group/card"
                                                                         wire:click="showCardDetail('{{ $card->id }}')">
                                                                        <div class="h-8 w-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded flex items-center justify-center mr-3 shadow-sm group-hover/card:ring-2 group-hover/card:ring-blue-400/30 transition-all">
                                                                            <svg class="h-5 w-5 text-slate-700 dark:text-slate-300 group-hover/card:text-blue-600 dark:group-hover/card:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-xs font-bold text-slate-700 dark:text-slate-200 font-mono tracking-wider group-hover/card:text-blue-600 dark:group-hover/card:text-blue-400 transition-colors">{{ $card->card_number }}</div>
                                                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center mt-0.5">
                                                                                <span class="uppercase">{{ $card->card_type ?? 'Debit' }}</span>
                                                                                <span class="mx-1">•</span>
                                                                                <span>Exp: {{ $card->valid_thru ?? ($card->expiry_date ? $card->expiry_date->format('m/y') : '-') }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="ml-auto opacity-0 group-hover/card:opacity-100 transition-opacity">
                                                                             <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $accounts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Customer Modal -->
    <x-modal name="customer-modal" :show="$showCustomerModal" wire:model.live="showCustomerModal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Customer Details
            </h2>
            
            @if($selectedCustomer)
                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Name</p>
                            <p class="text-gray-900">{{ $selectedCustomer->full_name }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">NIK</p>
                            <p class="text-gray-900 font-mono">{{ $selectedCustomer->nik }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $selectedCustomer->email }}</p>
                        </div>
                        <div>
                             <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="text-gray-900">{{ $selectedCustomer->phone_number }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">Mother Name</p>
                            <p class="text-gray-900">{{ $selectedCustomer->mother_name }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm font-medium text-gray-500 mb-1">Address</p>
                        <p class="text-gray-900">{{ $selectedCustomer->address }}</p>
                        <p class="text-gray-600 text-sm mt-1">
                            {{ $selectedCustomer->village }}, {{ $selectedCustomer->district }}<br>
                            {{ $selectedCustomer->regency }}, {{ $selectedCustomer->province }}
                        </p>
                    </div>

                    @if($selectedCustomer->note)
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm font-medium text-gray-500">Note</p>
                            <p class="text-gray-900 bg-yellow-50 p-2 rounded text-sm mt-1">{{ $selectedCustomer->note }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Close
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    <!-- Card Modal -->
    <x-modal name="card-modal" :show="$showCardModal" wire:model.live="showCardModal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="h-6 w-6 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Card Details
            </h2>
            
            @if($selectedCard)
                 <div class="mt-6">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-6 text-white shadow-xl max-w-sm mx-auto mb-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                        <div class="flex justify-between items-start mb-8">
                            <div class="text-xs text-slate-300 tracking-widest uppercase">
                                {{ $selectedCard->card_type ?? 'Debit Card' }}
                            </div>
                            <svg class="h-8 w-12 text-white" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="48" height="48" rx="8" fill="white" fill-opacity="0.1"/>
                                <path d="M14 16H34" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14 24H26" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14 32H20" stroke="white" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="mb-6">
                            <div class="text-2xl font-mono tracking-widest text-shadow">{{ chunk_split($selectedCard->card_number, 4, ' ') }}</div>
                        </div>
                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Card Holder</div>
                                <div class="font-medium tracking-wide uppercase text-sm">{{ $selectedCard->account->customer->full_name ?? 'VALUED CUSTOMER' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Expires</div>
                                <div class="font-mono">{{ $selectedCard->expiry_date ? $selectedCard->expiry_date->format('m/y') : 'MM/YY' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Additional Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                             <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Bank Name</p>
                                <p class="text-lg font-bold text-gray-800">{{ $selectedCard->account->bank_name ?? '-' }}</p>
                            </div>
                             <div class="text-right p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Account Number</p>
                                <p class="text-lg font-bold text-gray-800 font-mono tracking-wide">{{ $selectedCard->account->account_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($selectedCard->notes)
                        <div class="mt-4 bg-yellow-50 p-3 rounded-md border border-yellow-100">
                             <p class="text-xs font-bold text-yellow-700 uppercase tracking-wide mb-1">Confidential Notes</p>
                             <p class="text-sm text-gray-800 font-mono">{{ $selectedCard->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="closeModal">
                    Close
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
