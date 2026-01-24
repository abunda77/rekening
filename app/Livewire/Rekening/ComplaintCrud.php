<?php

namespace App\Livewire\Rekening;

use App\Models\Agent;
use App\Models\Complaint;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Help Desk - Pengaduan')]
class ComplaintCrud extends Component
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
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'nullable|exists:agents,id',
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
            $complaint = Complaint::findOrFail($id);
            $this->customer_id = $complaint->customer_id;
            $this->agent_id = $complaint->agent_id ?? '';
            $this->subject = $complaint->subject;
            $this->description = $complaint->description ?? '';
            $this->status = $complaint->status;
        } else {
            $this->reset(['customer_id', 'agent_id', 'subject', 'description']);
            $this->status = 'pending';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'customer_id', 'agent_id', 'subject', 'description', 'status']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'customer_id' => $this->customer_id,
            'agent_id' => $this->agent_id ?: null,
            'subject' => $this->subject,
            'description' => $this->description ?: null,
            'status' => $this->status,
        ];

        if ($this->editId) {
            Complaint::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pengaduan berhasil diperbarui.');
        } else {
            Complaint::create($data);
            session()->flash('success', 'Pengaduan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function updateStatus(string $id, string $status): void
    {
        Complaint::findOrFail($id)->update(['status' => $status]);
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
            Complaint::findOrFail($this->deleteId)->delete();
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
            ->with(['customer', 'agent'])
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', '%'.$this->search.'%')
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
        Complaint::whereIn('id', $this->selected)->delete();

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

        return view('livewire.rekening.complaint-crud', [
            'complaints' => $complaints,
            'customers' => Customer::orderBy('full_name')->get(),
            'agents' => Agent::orderBy('agent_name')->get(),
        ]);
    }
}
