<flux:main>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        {{-- Header Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <flux:heading size="xl">My Notes</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Manage your personal notes</flux:text>
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
                        placeholder="Search notes..." 
                        icon="magnifying-glass"
                        class="w-72"
                    />
                    @if(auth()->user()->hasRole('Super Admin'))
                         <flux:select wire:model.live="filterUser" class="w-40">
                            <option value="">All Users</option>
                            @foreach(($users ?? []) as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </flux:select>
                    @endif
                </div>
                <flux:button wire:click="openModal" variant="primary" icon="plus">
                    Add Note
                </flux:button>
            </div>
             <div class="flex items-center gap-2 mt-4 sm:mt-0 justify-end">
                @if(!empty($selected))
                    <flux:button wire:click="confirmBulkDelete" variant="danger" icon="trash">
                        Delete ({{ count($selected) }})
                    </flux:button>
                @endif
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
                            <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('title')">
                                Title
                                @if($sortField === 'title')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            @if(auth()->user()->hasRole('Super Admin'))
                                <th class="px-4 py-3 font-semibold">User</th>
                            @endif
                             <th class="px-4 py-3 font-semibold cursor-pointer" wire:click="sortBy('created_at')">
                                Created At
                                @if($sortField === 'created_at')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-4 py-3 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($notes as $note)
                            <tr wire:key="note-{{ $note->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="p-4 text-center">
                                    <flux:checkbox wire:model.live="selected" value="{{ $note->id }}" />
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $note->title }}
                                    <div class="text-xs text-zinc-500 truncate max-w-md">{{ Str::limit($note->content, 50) }}</div>
                                </td>
                                 @if(auth()->user()->hasRole('Super Admin'))
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                        {{ $note->user->name }}
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ $note->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <flux:button wire:click="view('{{ $note->id }}')" size="sm" variant="ghost" icon="eye" />
                                        <flux:button wire:click="openModal('{{ $note->id }}')" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button wire:click="confirmDelete('{{ $note->id }}')" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('Super Admin') ? 5 : 4 }}" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    No notes found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Section --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            {{ $notes->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        <flux:modal wire:model="showModal" name="note-modal" class="max-w-2xl">
            <div class="space-y-6">
                <flux:heading size="lg">{{ $editId ? 'Edit Note' : 'Add New Note' }}</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>Title</flux:label>
                        <flux:input wire:model="title" placeholder="Note title" />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Content</flux:label>
                        <flux:textarea wire:model="content" placeholder="Write your note here..." rows="6" />
                        <flux:error name="content" />
                    </flux:field>

                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button wire:click="closeModal" variant="ghost">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $editId ? 'Save Changes' : 'Create Note' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        {{-- Delete Confirmation Modal --}}
        <flux:modal wire:model="showDeleteModal" name="delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Delete Confirmation</flux:heading>
                <flux:text>Are you sure you want to delete this note?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelDelete" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="delete" variant="danger">Delete</flux:button>
                </div>
            </div>
        </flux:modal>
        
         {{-- Bulk Delete Confirmation Modal --}}
        <flux:modal wire:model="showBulkDeleteModal" name="bulk-delete-modal" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">Bulk Delete Confirmation</flux:heading>
                <flux:text>Are you sure you want to delete {{ count($selected) }} selected notes?</flux:text>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="cancelBulkDelete" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="bulkDelete" variant="danger">Delete</flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- View Modal --}}
        <flux:modal wire:model="showViewModal" name="view-modal" class="md:w-full max-w-2xl">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Note Details</flux:heading>
                </div>

                @if($viewingNote)
                    <div class="space-y-4">
                         <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Title</span>
                            <p class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $viewingNote->title }}</p>
                        </div>
                        
                         <div class="space-y-1">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Content</span>
                             <div class="prose dark:prose-invert max-w-none p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-700">
                                <p class="whitespace-pre-wrap text-zinc-800 dark:text-zinc-200">{{ $viewingNote->content }}</p>
                             </div>
                        </div>

                         <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Created At</span>
                                <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingNote->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Updated At</span>
                                <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $viewingNote->updated_at->format('d M Y H:i') }}</p>
                            </div>
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
