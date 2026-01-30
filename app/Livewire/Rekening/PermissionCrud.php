<?php

namespace App\Livewire\Rekening;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Permission')]
class PermissionCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $selectedModule = null;

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 20;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedModule(): void
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

    public function getRowsQuery()
    {
        return Permission::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->when($this->selectedModule, function ($query) {
                // Filter by module (second word in permission name)
                $query->where('name', 'like', '% '.$this->selectedModule);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function getPermissionsByModule(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            // Extract module from permission name (e.g., "view users" -> "users")
            $parts = explode(' ', $permission->name);
            $module = $parts[1] ?? 'other';

            if (! isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    public function getModules(): array
    {
        $permissions = Permission::all();
        $modules = [];

        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $module = $parts[1] ?? 'other';

            if (! in_array($module, $modules)) {
                $modules[] = $module;
            }
        }

        sort($modules);

        return $modules;
    }

    public function getRolesWithPermission(string $permissionName): array
    {
        $permission = Permission::where('name', $permissionName)->first();

        if (! $permission) {
            return [];
        }

        return $permission->roles->pluck('name')->toArray();
    }

    public function render()
    {
        $permissions = $this->getRowsQuery()->paginate($this->perPage);
        $modules = $this->getModules();
        $permissionsByModule = $this->getPermissionsByModule();

        // Get roles for each permission
        $rolesByPermission = [];
        foreach ($permissions as $permission) {
            $rolesByPermission[$permission->name] = $this->getRolesWithPermission($permission->name);
        }

        return view('livewire.rekening.permission-crud', [
            'permissions' => $permissions,
            'modules' => $modules,
            'permissionsByModule' => $permissionsByModule,
            'rolesByPermission' => $rolesByPermission,
        ]);
    }
}
