<?php

namespace App\Livewire\Agent;

use App\Models\Account;
use App\Models\Shipment as ShipmentModel;
use App\Services\KlikResiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.agent')]
#[Title('Pengiriman')]
class Shipment extends Component
{
    use WithPagination;

    protected KlikResiService $klikResiService;

    public function boot(KlikResiService $klikResiService): void
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

    public ?string $account_id = '';

    public ?string $delivery_date = '';

    public string $expedition = '';

    public string $status = 'SENT';

    public string $receipt_number = '';

    public ?string $note = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    // Tracking
    public bool $showTrackingModal = false;

    public ?array $trackingResult = null;

    public bool $isLoadingTracking = false;

    // View Modal
    public bool $showViewModal = false;

    public ?ShipmentModel $viewShipment = null;

    protected function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'delivery_date' => 'nullable|date',
            'expedition' => 'required|string|max:100',
            'status' => 'required|in:SENT,PROCESS,OTW',
            'receipt_number' => 'required|string|max:100',
            'note' => 'nullable|string|max:500',
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
            $shipment = ShipmentModel::where('agent_id', Auth::guard('agent')->id())->findOrFail($id);
            $this->account_id = $shipment->account_id;
            $this->delivery_date = $shipment->delivery_date?->format('Y-m-d');
            $this->expedition = $shipment->expedition;
            $this->status = $shipment->status;
            $this->receipt_number = $shipment->receipt_number ?? '';
            $this->note = $shipment->note ?? '';
        } else {
            $this->reset(['account_id', 'delivery_date', 'expedition', 'receipt_number', 'note']);
            $this->status = 'SENT';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'account_id', 'delivery_date', 'expedition', 'status', 'receipt_number', 'note']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $account = Account::findOrFail($this->account_id);

        $data = [
            'account_id' => $this->account_id,
            'agent_id' => Auth::guard('agent')->id(),
            'delivery_date' => $this->delivery_date ?: null,
            'expedition' => $this->expedition,
            'status' => $this->status,
            'receipt_number' => $this->receipt_number,
            'note' => $this->note ?: null,
        ];

        if ($this->editId) {
            ShipmentModel::where('agent_id', Auth::guard('agent')->id())->findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pengiriman berhasil diperbarui.');
        } else {
            ShipmentModel::create($data);
            session()->flash('success', 'Pengiriman berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function updateStatus(string $id, string $status): void
    {
        ShipmentModel::where('agent_id', Auth::guard('agent')->id())->findOrFail($id)->update(['status' => $status]);
        session()->flash('success', 'Status pengiriman berhasil diubah.');
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            ShipmentModel::where('agent_id', Auth::guard('agent')->id())->findOrFail($this->deleteId)->delete();
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
        $shipment = ShipmentModel::where('agent_id', Auth::guard('agent')->id())->findOrFail($id);

        if (! $shipment->receipt_number || ! $shipment->expedition) {
            $this->js("Flux.toast({ text: 'Nomor resi atau ekspedisi belum diisi.', variant: 'danger' })");

            return;
        }

        $this->isLoadingTracking = true;
        // Reset previous result
        $this->trackingResult = null;
        $this->showTrackingModal = true;

        try {
            $result = $this->klikResiService->track($shipment->receipt_number, $shipment->expedition);

            if (isset($result['status']['code']) && $result['status']['code'] == 200) {
                $this->trackingResult = $result['data'];
            } else {
                $message = $result['status']['message'] ?? 'Gagal melacak resi.';
                $this->trackingResult = ['error' => $message];
            }
        } catch (\Exception $e) {
            Log::error('Tracking error: '.$e->getMessage());
            $this->trackingResult = ['error' => 'Terjadi kesalahan saat melacak resi.'];
        }

        $this->isLoadingTracking = false;
    }

    public function closeTrackingModal(): void
    {
        $this->showTrackingModal = false;
        $this->trackingResult = null;
    }

    public function openViewModal(string $id): void
    {
        $this->viewShipment = ShipmentModel::where('agent_id', Auth::guard('agent')->id())
            ->with(['account.customer'])
            ->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewShipment = null;
    }

    public function getRowsQuery()
    {
        return ShipmentModel::query()
            ->where('agent_id', Auth::guard('agent')->id())
            ->with(['account.customer'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('receipt_number', 'like', '%'.$this->search.'%')
                        ->orWhere('expedition', 'like', '%'.$this->search.'%')
                        ->orWhereHas('account', function ($aq) {
                            $aq->where('account_number', 'like', '%'.$this->search.'%')
                                ->orWhere('bank_name', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('account.customer', function ($cq) {
                            $cq->where('full_name', 'like', '%'.$this->search.'%');
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
        ShipmentModel::where('agent_id', Auth::guard('agent')->id())->whereIn('id', $this->selected)->delete();

        session()->flash('success', count($this->selected).' pengiriman berhasil dihapus.');

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
        $shipments = $this->getRowsQuery()->paginate($this->perPage);

        return view('livewire.agent.shipment', [
            'shipments' => $shipments,
            'accounts' => Account::where('agent_id', Auth::guard('agent')->id())->with('customer')->orderBy('bank_name')->get(),
            'couriers' => $this->klikResiService->getCouriers(),
        ]);
    }
}
