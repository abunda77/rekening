<?php

namespace App\Livewire\Rekening;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Role')]
class RoleCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $roleName = '';

    public array $selectedPermissions = [];

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
            'roleName' => 'required|string|max:255|unique:roles,name,'.$this->editId,
            'selectedPermissions' => 'array',
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
            $role = Role::findOrFail($id);
            $this->roleName = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        } else {
            $this->reset(['roleName', 'selectedPermissions']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'roleName', 'selectedPermissions']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            $role = Role::findOrFail($this->editId);
            $role->name = $this->roleName;
            $role->save();
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('success', 'Role berhasil diperbarui.');
        } else {
            $role = Role::create(['name' => $this->roleName, 'guard_name' => 'web']);
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('success', 'Role berhasil ditambahkan.');
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
            $role = Role::findOrFail($this->deleteId);

            // Prevent deleting Super Admin role
            if ($role->name === 'Super Admin') {
                session()->flash('error', 'Role Super Admin tidak dapat dihapus.');
                $this->showDeleteModal = false;
                $this->deleteId = null;

                return;
            }

            $role->delete();
            session()->flash('success', 'Role berhasil dihapus.');
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
        return Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->withCount(['permissions', 'users']);
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
        // Filter out Super Admin roles from bulk delete
        $roles = Role::whereIn('id', $this->selected)->get();
        $deletableIds = $roles->reject(fn ($role) => $role->name === 'Super Admin')->pluck('id');

        Role::whereIn('id', $deletableIds)->delete();

        session()->flash('success', $deletableIds->count().' role berhasil dihapus.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function getPermissionsByModule(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            // Extract module from permission name (e.g., "view users" -> "user")
            $parts = explode(' ', $permission->name);
            $action = $parts[0] ?? '';
            $module = $parts[1] ?? 'other';

            if (! isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    public function render()
    {
        $roles = $this->getRowsQuery()->paginate($this->perPage);
        $permissionsByModule = $this->getPermissionsByModule();

        return view('livewire.rekening.role-crud', [
            'roles' => $roles,
            'permissionsByModule' => $permissionsByModule,
        ]);
    }
}
