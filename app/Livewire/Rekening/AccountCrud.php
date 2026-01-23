<?php

namespace App\Livewire\Rekening;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Rekening')]
class AccountCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public string $filterStatus = '';

    // Form fields
    public ?string $editId = null;

    public string $customer_id = '';

    public string $agent_id = '';

    public string $bank_name = '';

    public string $branch = '';

    public string $account_number = '';

    public ?string $opening_date = null;

    public string $note = '';

    public string $status = 'aktif';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'nullable|exists:agents,id',
            'bank_name' => 'required|string|max:100',
            'branch' => 'nullable|string|max:100',
            'account_number' => 'required|string|max:50|unique:accounts,account_number,'.$this->editId,
            'opening_date' => 'nullable|date',
            'note' => 'nullable|string',
            'status' => 'required|in:aktif,bermasalah,nonaktif',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
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
            $account = Account::findOrFail($id);
            $this->customer_id = $account->customer_id;
            $this->agent_id = $account->agent_id ?? '';
            $this->bank_name = $account->bank_name;
            $this->branch = $account->branch ?? '';
            $this->account_number = $account->account_number;
            $this->opening_date = $account->opening_date?->format('Y-m-d');
            $this->note = $account->note ?? '';
            $this->status = $account->status;
        } else {
            $this->reset(['customer_id', 'agent_id', 'bank_name', 'branch', 'account_number', 'opening_date', 'note']);
            $this->status = 'aktif';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'customer_id', 'agent_id', 'bank_name', 'branch', 'account_number', 'opening_date', 'note', 'status']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'customer_id' => $this->customer_id,
            'agent_id' => $this->agent_id ?: null,
            'bank_name' => $this->bank_name,
            'branch' => $this->branch ?: null,
            'account_number' => $this->account_number,
            'opening_date' => $this->opening_date ?: null,
            'note' => $this->note ?: null,
            'status' => $this->status,
        ];

        if ($this->editId) {
            Account::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Rekening berhasil diperbarui.');
        } else {
            Account::create($data);
            session()->flash('success', 'Rekening berhasil ditambahkan.');
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
            Account::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Rekening berhasil dihapus.');
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
        $accounts = Account::query()
            ->with(['customer', 'agent'])
            ->when($this->search, function ($query) {
                $query->where('account_number', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('full_name', 'like', '%'.$this->search.'%')
                            ->orWhere('nik', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.rekening.account-crud', [
            'accounts' => $accounts,
            'customers' => Customer::orderBy('full_name')->get(),
            'agents' => Agent::orderBy('agent_name')->get(),
        ]);
    }
}
