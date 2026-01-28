<?php

namespace App\Livewire\Rekening;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Pengiriman')]
class ShipmentCrud extends Component
{
    use WithPagination;

    protected $klikResiService;

    public function boot(\App\Services\KlikResiService $klikResiService)
    {
        $this->klikResiService = $klikResiService;
    }

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public string $filterStatus = '';

    // Form fields
    public ?string $editId = null;

    public string $agent_id = '';

    public string $account_id = '';

    public ?string $delivery_date = null;

    public string $expedition = '';

    public string $status = 'SENT';

    public ?string $receipt_number = '';

    public string $note = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Tracking
    public bool $showTrackingModal = false;

    public ?array $trackingResult = null;

    public bool $isLoadingTracking = false;

    // View
    public bool $showViewModal = false;

    public ?Shipment $viewShipment = null;

    protected function rules(): array
    {
        return [
            'agent_id' => 'required|exists:agents,id',
            'account_id' => 'required|exists:accounts,id',
            'delivery_date' => 'required|date',
            'expedition' => 'required|string|max:100',
            'status' => 'required|in:SENT,PROCESS,OTW',
            'receipt_number' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function updatedAgentId(): void
    {
        $this->account_id = '';
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
            $shipment = Shipment::findOrFail($id);
            $this->agent_id = $shipment->agent_id;
            $this->account_id = $shipment->account_id;
            $this->delivery_date = $shipment->delivery_date?->format('Y-m-d');
            $this->expedition = $shipment->expedition;
            $this->status = $shipment->status;
            $this->receipt_number = $shipment->receipt_number ?? '';
            $this->note = $shipment->note ?? '';
        } else {
            $this->reset(['agent_id', 'account_id', 'delivery_date', 'expedition', 'status', 'receipt_number', 'note']);
            $this->status = 'SENT';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'agent_id', 'account_id', 'delivery_date', 'expedition', 'status', 'receipt_number', 'note']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'agent_id' => $this->agent_id,
            'account_id' => $this->account_id,
            'delivery_date' => $this->delivery_date,
            'expedition' => $this->expedition,
            'status' => $this->status,
            'receipt_number' => $this->receipt_number ?: null,
            'note' => $this->note ?: null,
        ];

        if ($this->editId) {
            Shipment::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pengiriman berhasil diperbarui.');
        } else {
            Shipment::create($data);
            session()->flash('success', 'Pengiriman berhasil ditambahkan.');
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
            Shipment::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Pengiriman berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function trackShipment(string $id): void
    {
        $shipment = Shipment::findOrFail($id);

        if (! $shipment->receipt_number || ! $shipment->expedition) {
            $this->js("Flux.toast({ text: 'Nomor resi atau ekspedisi belum diisi.', variant: 'danger' })");

            return;
        }

        $this->isLoadingTracking = true;
        // Reset previous result
        $this->trackingResult = null;
        $this->showTrackingModal = true;

        $result = $this->klikResiService->track($shipment->receipt_number, $shipment->expedition);

        if (isset($result['status']['code']) && $result['status']['code'] == 200) {
            $this->trackingResult = $result['data'];
        } else {
            $message = $result['status']['message'] ?? 'Gagal melacak resi.';
            $this->trackingResult = ['error' => $message];
        }

        $this->isLoadingTracking = false;
    }

    public function closeTrackingModal(): void
    {
        $this->showTrackingModal = false;
        $this->trackingResult = null;
    }

    public function view(string $id): void
    {
        $this->viewShipment = Shipment::with(['agent', 'account'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewShipment = null;
    }

    public function getRowsQuery()
    {
        return Shipment::query()
            ->with(['agent', 'account'])
            ->when($this->search, function ($query) {
                $query->where('receipt_number', 'like', '%'.$this->search.'%')
                    ->orWhere('expedition', 'like', '%'.$this->search.'%')
                    ->orWhereHas('agent', function ($q) {
                        $q->where('agent_name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $shipments = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.rekening.shipment-crud', [
            'shipments' => $shipments,
            'agents' => Agent::orderBy('agent_name')->get(),
            'accounts' => $this->agent_id
                ? Account::where('agent_id', $this->agent_id)->orderBy('account_number')->get()
                : collect(),
            'couriers' => $this->klikResiService->getCouriers(),
        ]);
    }
}
