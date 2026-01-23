<?php

namespace App\Livewire\Rekening;

use App\Models\Agent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Agent')]
class AgentCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $agent_code = '';

    public string $agent_name = '';

    public string $usertelegram = '';

    public string $password = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    protected function rules(): array
    {
        return [
            'agent_code' => 'required|string|max:50|unique:agents,agent_code,'.$this->editId,
            'agent_name' => 'required|string|max:255',
            'usertelegram' => 'nullable|string|max:100',
            'password' => $this->editId ? 'nullable|string|min:6' : 'required|string|min:6',
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
            $agent = Agent::findOrFail($id);
            $this->agent_code = $agent->agent_code;
            $this->agent_name = $agent->agent_name;
            $this->usertelegram = $agent->usertelegram ?? '';
            $this->password = '';
        } else {
            $this->reset(['agent_code', 'agent_name', 'usertelegram', 'password']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'agent_code', 'agent_name', 'usertelegram', 'password']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'agent_code' => $this->agent_code,
            'agent_name' => $this->agent_name,
            'usertelegram' => $this->usertelegram ?: null,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->editId) {
            Agent::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Agent berhasil diperbarui.');
        } else {
            Agent::create($data);
            session()->flash('success', 'Agent berhasil ditambahkan.');
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
            Agent::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Agent berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $agents = Agent::query()
            ->when($this->search, function ($query) {
                $query->where('agent_code', 'like', '%'.$this->search.'%')
                    ->orWhere('agent_name', 'like', '%'.$this->search.'%')
                    ->orWhere('usertelegram', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.rekening.agent-crud', [
            'agents' => $agents,
        ]);
    }
}
