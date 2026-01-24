<?php

namespace App\Livewire\Agent;

use App\Models\Account;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.agent')]
#[Title('Help Desk Agent')]
class HelpDesk extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public string $filterStatus = '';

    // Form fields
    public ?string $editId = null;

    public string $account_id = '';

    // agent_id is auto-filled

    public string $subject = '';

    public string $description = '';

    public string $status = 'pending';

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
            'account_id' => 'required|exists:accounts,id',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,processing,resolved',
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
            $complaint = Complaint::where('agent_id', Auth::guard('agent')->id())->findOrFail($id);
            $this->account_id = $complaint->account_id;
            $this->subject = $complaint->subject;
            $this->description = $complaint->description ?? '';
            $this->status = $complaint->status;
        } else {
            $this->reset(['account_id', 'subject', 'description']);
            $this->status = 'pending';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'account_id', 'subject', 'description', 'status']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $account = Account::findOrFail($this->account_id);

        $data = [
            'customer_id' => $account->customer_id,
            'account_id' => $this->account_id,
            'agent_id' => Auth::guard('agent')->id(),
            'subject' => $this->subject,
            'description' => $this->description ?: null,
            'status' => $this->status,
        ];

        if ($this->editId) {
            Complaint::where('agent_id', Auth::guard('agent')->id())->findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pengaduan berhasil diperbarui.');
        } else {
            Complaint::create($data);
            session()->flash('success', 'Pengaduan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function updateStatus(string $id, string $status): void
    {
        Complaint::where('agent_id', Auth::guard('agent')->id())->findOrFail($id)->update(['status' => $status]);
        session()->flash('success', 'Status pengaduan berhasil diubah.');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Complaint::where('agent_id', Auth::guard('agent')->id())->findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Pengaduan berhasil dihapus.');
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
        return Complaint::query()
            ->where('agent_id', Auth::guard('agent')->id())
            ->with(['customer'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('subject', 'like', '%'.$this->search.'%')
                        ->orWhereHas('customer', function ($cq) {
                            $cq->where('full_name', 'like', '%'.$this->search.'%')
                                ->orWhere('nik', 'like', '%'.$this->search.'%');
                        });
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
        Complaint::where('agent_id', Auth::guard('agent')->id())->whereIn('id', $this->selected)->delete();

        session()->flash('success', count($this->selected).' pengaduan berhasil dihapus.');

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
        $complaints = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.agent.help-desk', [
            'complaints' => $complaints,
            'accounts' => Account::where('agent_id', Auth::guard('agent')->id())->with('customer')->orderBy('bank_name')->get(),
        ]);
    }
}
