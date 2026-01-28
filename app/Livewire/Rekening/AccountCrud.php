<?php

namespace App\Livewire\Rekening;

use App\Exports\AccountsExport;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Rekening')]
class AccountCrud extends Component
{
    use WithFileUploads, WithPagination;

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

    public ?string $expired_on = null;

    public string $note = '';

    public ?string $mobile_banking = '';

    public $cover_buku; // File upload property

    public string $status = 'aktif';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    // View Modal
    public bool $showViewModal = false;

    public ?Account $viewingAccount = null;

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
            'LAINNYA' => 'Lainnya',
        ];
    }

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'nullable|exists:agents,id',
            'bank_name' => 'required|string|max:100',
            'branch' => 'nullable|string|max:100',
            'account_number' => 'required|string|max:50|unique:accounts,account_number,'.$this->editId,
            'opening_date' => 'nullable|date',
            'expired_on' => 'nullable|date|after_or_equal:opening_date',
            'note' => 'nullable|string',
            'mobile_banking' => 'nullable|string',
            'cover_buku' => 'nullable|image|max:2048', // 2MB Max
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
            $this->expired_on = $account->expired_on?->format('Y-m-d');
            $this->note = $account->note ?? '';
            $this->mobile_banking = $account->mobile_banking ?? '';
            $this->status = $account->status;
        } else {
            $this->reset(['customer_id', 'agent_id', 'bank_name', 'branch', 'account_number', 'opening_date', 'expired_on', 'note', 'mobile_banking', 'cover_buku']);
            $this->status = 'aktif';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'customer_id', 'agent_id', 'bank_name', 'branch', 'account_number', 'opening_date', 'expired_on', 'note', 'mobile_banking', 'status', 'cover_buku']);
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
            'expired_on' => $this->expired_on ?: null,
            'note' => $this->note ?: null,
            'mobile_banking' => $this->mobile_banking ?: null,
            'status' => $this->status,
        ];

        if ($this->cover_buku) {
            $data['cover_buku'] = $this->cover_buku->store('accounts', 'public');
        }

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

    public function view(string $id): void
    {
        $this->viewingAccount = Account::with(['customer', 'agent'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingAccount = null;
    }

    public function getRowsQuery()
    {
        return Account::query()
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
        Account::whereIn('id', $this->selected)->delete();

        session()->flash('success', count($this->selected).' rekening berhasil dihapus.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function exportXlsx()
    {
        return Excel::download(new AccountsExport, 'accounts.xlsx');
    }

    public function exportPdf()
    {
        $accounts = Account::query()->with(['customer', 'agent'])->latest()->get();
        $pdf = Pdf::loadView('exports.accounts-pdf', ['accounts' => $accounts]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'accounts.pdf');
    }

    public function printDetailPdf(string $id)
    {
        $account = Account::with(['customer', 'agent'])->findOrFail($id);
        $pdf = Pdf::loadView('exports.account-detail-pdf', ['account' => $account]);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'account_'.$account->account_number.'.pdf');
    }

    public function render()
    {
        $accounts = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.rekening.account-crud', [
            'accounts' => $accounts,
            'customers' => Customer::orderBy('full_name')->get(),
            'agents' => Agent::orderBy('agent_name')->get(),
        ]);
    }
}
