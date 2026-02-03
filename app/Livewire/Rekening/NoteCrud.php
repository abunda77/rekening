<?php

namespace App\Livewire\Rekening;

use App\Models\Note;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('My Notes')]
class NoteCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Filter
    public string $filterUser = '';

    // Form fields
    public ?string $editId = null;

    public string $title = '';

    public string $content = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    // View Modal
    public bool $showViewModal = false;

    public ?Note $viewingNote = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUser(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openModal(?string $id = null): void
    {
        $this->resetValidation();
        $this->editId = $id;

        if ($id) {
            $note = Note::findOrFail($id);
            if (! auth()->user()->hasRole('Super Admin') && $note->user_id !== auth()->id()) {
                abort(403);
            }

            $this->title = $note->title;
            $this->content = $note->content ?? '';
        } else {
            $this->reset(['title', 'content']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'title', 'content']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content ?: null,
        ];

        if ($this->editId) {
            $note = Note::findOrFail($this->editId);
            if (! auth()->user()->hasRole('Super Admin') && $note->user_id !== auth()->id()) {
                abort(403);
            }
            $note->update($data);
            session()->flash('success', 'Note updated successfully.');
        } else {
            $data['user_id'] = auth()->id();
            Note::create($data);
            session()->flash('success', 'Note created successfully.');
        }

        $this->closeModal();
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $note = Note::findOrFail($this->deleteId);
            if (! auth()->user()->hasRole('Super Admin') && $note->user_id !== auth()->id()) {
                abort(403);
            }
            $note->delete();
            session()->flash('success', 'Note deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function view(string $id): void
    {
        $this->viewingNote = Note::with('user')->findOrFail($id);
        if (! auth()->user()->hasRole('Super Admin') && $this->viewingNote->user_id !== auth()->id()) {
            abort(403);
        }
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingNote = null;
    }

    public function getRowsQuery()
    {
        $query = Note::query()
            ->with('user');

        if (! auth()->user()->hasRole('Super Admin')) {
            $query->where('user_id', auth()->id());
        } else {
            // Admin can filter by user
            $query->when($this->filterUser, function ($q) {
                $q->where('user_id', $this->filterUser);
            });
        }

        return $query
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getRowsQuery()->paginate($this->perPage)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected(): void
    {
        $this->selectAll = false;
    }

    public function confirmBulkDelete(): void
    {
        if (! empty($this->selected)) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function bulkDelete(): void
    {
        // Must respect user ownership
        $query = Note::whereIn('id', $this->selected);
        if (! auth()->user()->hasRole('Super Admin')) {
            $query->where('user_id', auth()->id());
        }
        $query->delete();

        session()->flash('success', count($this->selected).' notes deleted successfully.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function render()
    {
        $notes = $this->getRowsQuery()->paginate($this->perPage);

        $users = [];
        if (auth()->user() && auth()->user()->hasRole('Super Admin')) {
            $users = User::orderBy('name')->get();
        }

        return view('livewire.rekening.note-crud', [
            'notes' => $notes,
            'users' => $users,
        ]);
    }
}
