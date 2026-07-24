<?php

namespace App\Livewire\Rekening;

use App\Models\Agent;
use App\Models\CompanyAccount;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Rekening PT')]
class CompanyAccountCrud extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public ?string $editId = null;

    public string $customer_id = '';

    public string $agent_id = '';

    public string $company_name = '';

    public string $bank_name = '';

    public string $branch = '';

    public string $account_number = '';

    public ?string $opening_date = null;

    public ?string $expired_on = null;

    public string $mobile_banking = '';

    public string $note = '';

    public $cover_buku;

    public string $status = 'aktif';

    public bool $showModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    public ?CompanyAccount $viewingAccount = null;

    public function getBanksProperty(): array
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'MANDIRI' => 'Bank Mandiri',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BTN' => 'Bank Tabungan Negara (BTN)',
            'CIMB' => 'CIMB Niaga',
            'DANAMON' => 'Bank Danamon',
            'PERMATA' => 'Bank Permata',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'PANIN' => 'Panin Bank',
            'MAYBANK' => 'Maybank Indonesia',
            'OCBC' => 'OCBC NISP',
            'MEGA' => 'Bank Mega',
            'LAINNYA' => 'Lainnya',
        ];
    }

    protected function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'agent_id' => 'nullable|exists:agents,id',
            'company_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:100',
            'branch' => 'nullable|string|max:100',
            'account_number' => 'required|string|max:50|unique:company_accounts,account_number,'.$this->editId,
            'opening_date' => 'nullable|date',
            'expired_on' => 'nullable|date|after_or_equal:opening_date',
            'mobile_banking' => 'nullable|string',
            'note' => 'nullable|string',
            'cover_buku' => 'nullable|image|max:2048',
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
        $allowedFields = ['company_name', 'account_number', 'bank_name', 'status', 'opening_date', 'expired_on', 'created_at'];

        abort_unless(in_array($field, $allowedFields, true), 400);

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
        $this->resetForm();
        $this->editId = $id;

        if ($id) {
            $account = CompanyAccount::findOrFail($id);
            $this->customer_id = $account->customer_id ?? '';
            $this->agent_id = $account->agent_id ?? '';
            $this->company_name = $account->company_name;
            $this->bank_name = $account->bank_name;
            $this->branch = $account->branch ?? '';
            $this->account_number = $account->account_number;
            $this->opening_date = $account->opening_date?->format('Y-m-d');
            $this->expired_on = $account->expired_on?->format('Y-m-d');
            $this->mobile_banking = $account->mobile_banking ?? '';
            $this->note = $account->note ?? '';
            $this->status = $account->status;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'customer_id' => $this->customer_id ?: null,
            'agent_id' => $this->agent_id ?: null,
            'company_name' => $this->company_name,
            'bank_name' => $this->bank_name,
            'branch' => $this->branch ?: null,
            'account_number' => $this->account_number,
            'opening_date' => $this->opening_date ?: null,
            'expired_on' => $this->expired_on ?: null,
            'mobile_banking' => $this->mobile_banking ?: null,
            'note' => $this->note ?: null,
            'status' => $this->status,
        ];

        if ($this->cover_buku) {
            $data['cover_buku'] = $this->cover_buku->store('company-accounts', 'public');
        }

        CompanyAccount::query()->updateOrCreate(['id' => $this->editId], $data);

        session()->flash('success', $this->editId
            ? 'Rekening PT berhasil diperbarui.'
            : 'Rekening PT berhasil ditambahkan.');

        $this->closeModal();
    }

    public function view(string $id): void
    {
        $this->viewingAccount = CompanyAccount::with(['customer', 'agent'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingAccount = null;
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            CompanyAccount::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Rekening PT berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    private function rowsQuery(): Builder
    {
        return CompanyAccount::query()
            ->with(['customer', 'agent'])
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('company_name', 'like', '%'.$this->search.'%')
                        ->orWhere('account_number', 'like', '%'.$this->search.'%')
                        ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('customer', fn (Builder $customer) => $customer->where('full_name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->filterStatus, fn (Builder $query) => $query->where('status', $this->filterStatus))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function resetForm(): void
    {
        $this->reset([
            'customer_id', 'agent_id', 'company_name', 'bank_name', 'branch', 'account_number',
            'opening_date', 'expired_on', 'mobile_banking', 'note', 'cover_buku',
        ]);
        $this->status = 'aktif';
    }

    public function render()
    {
        return view('livewire.rekening.company-account-crud', [
            'accounts' => $this->rowsQuery()->paginate($this->perPage),
            'customers' => Customer::query()->orderBy('full_name')->get(),
            'agents' => Agent::query()->orderBy('agent_name')->get(),
        ]);
    }
}
