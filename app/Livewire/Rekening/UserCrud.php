<?php

namespace App\Livewire\Rekening;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen User')]
class UserCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public array $selectedRoles = [];

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->editId),
            ],
            'password' => $this->editId ? 'nullable|string|min:8' : 'required|string|min:8',
            'selectedRoles' => 'array',
        ];
    }

    public function updatedSearch(): void
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
            $user = User::findOrFail($id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->password = '';
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
        } else {
            $this->reset(['name', 'email', 'password', 'selectedRoles']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'name', 'email', 'password', 'selectedRoles']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editId) {
            $user = User::findOrFail($this->editId);
            $user->update($data);
            $user->syncRoles($this->selectedRoles);
            session()->flash('success', 'User berhasil diperbarui.');
        } else {
            $user = User::create($data);
            $user->syncRoles($this->selectedRoles);
            session()->flash('success', 'User berhasil ditambahkan.');
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
            $user = User::findOrFail($this->deleteId);

            // Prevent deleting current authenticated user
            if ($user->id === auth()->id()) {
                session()->flash('error', 'Anda tidak dapat menghapus akun sendiri.');
                $this->showDeleteModal = false;
                $this->deleteId = null;

                return;
            }

            // Prevent deleting Super Admin user
            if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
                session()->flash('error', 'Tidak dapat menghapus user Super Admin terakhir.');
                $this->showDeleteModal = false;
                $this->deleteId = null;

                return;
            }

            $user->delete();
            session()->flash('success', 'User berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function getRowsQuery()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with('roles');
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
        // Filter out current user and last Super Admin from bulk delete
        $users = User::whereIn('id', $this->selected)->get();
        $deletableIds = $users->reject(function ($user) {
            // Cannot delete self
            if ($user->id === auth()->id()) {
                return true;
            }
            // Cannot delete last Super Admin
            if ($user->hasRole('Super Admin') && User::role('Super Admin')->count() <= 1) {
                return true;
            }

            return false;
        })->pluck('id');

        User::whereIn('id', $deletableIds)->delete();

        session()->flash('success', $deletableIds->count().' user berhasil dihapus.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function getAvailableRoles()
    {
        return Role::all();
    }

    public function render()
    {
        $users = $this->getRowsQuery()->paginate($this->perPage);
        $roles = $this->getAvailableRoles();

        return view('livewire.rekening.user-crud', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
