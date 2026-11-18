<?php

namespace App\Livewire\Rekening;

use App\Models\AgentSupplier;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Agent Supplier')]
class AgentSupplierCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $name = '';

    public string $contact_number = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:agent_suppliers,name,'.$this->editId,
            'contact_number' => 'nullable|string|max:30',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama agent supplier wajib diisi.',
            'name.unique' => 'Nama agent supplier sudah terdaftar.',
            'contact_number.max' => 'No. kontak maksimal 30 karakter.',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        abort_unless(in_array($field, ['name', 'contact_number', 'created_at'], true), 400);

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
            $supplier = AgentSupplier::findOrFail($id);
            $this->name = $supplier->name;
            $this->contact_number = $supplier->contact_number ?? '';
        } else {
            $this->reset(['name', 'contact_number']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editId = null;
        $this->reset(['name', 'contact_number']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'contact_number' => $this->contact_number ?: null,
        ];

        if ($this->editId) {
            AgentSupplier::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Agent supplier berhasil diperbarui.');
        } else {
            AgentSupplier::create($data);
            session()->flash('success', 'Agent supplier berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            AgentSupplier::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Agent supplier berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    private function rowsQuery(): Builder
    {
        return AgentSupplier::query()
            ->withCount(['accounts', 'companyAccounts'])
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('contact_number', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.rekening.agent-supplier-crud', [
            'suppliers' => $this->rowsQuery()->paginate($this->perPage),
        ]);
    }
}
