<?php

namespace App\Livewire\Rekening;

use App\Models\Account;
use App\Models\Card;
use App\Exports\CardsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Kartu ATM')]
class CardCrud extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $account_id = '';

    public string $card_number = '';

    public ?string $expiry_date = null;

    public string $card_type = '';

    public string $notes = "CVV :\nPIN :";

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // View Modal
    public bool $showViewModal = false;

    public ?Card $viewingCard = null;

    // Searchable Select
    public string $accountSearch = '';

    public bool $isSearching = false;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    protected function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'card_number' => 'required|string|max:20|unique:cards,card_number,'.$this->editId,
            'expiry_date' => 'nullable|date',
            'card_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAccountSearch(): void
    {
        // Jika user mengetik, kita buka dropdown dan reset pilihan (opsional, tergantung UX yg dimau)
        // Disini kita biarkan account_id tetap kecuali user memilih ulang,
        // tapi visual indikasi nanti ada di UI
        $this->isSearching = true;
    }

    public function selectAccount(string $id, string $displayText): void
    {
        $this->account_id = $id;
        $this->accountSearch = $displayText;
        $this->isSearching = false;
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
        $this->isSearching = false;

        if ($id) {
            $card = Card::findOrFail($id);
            $this->account_id = $card->account_id;

            // Set initial search text for display
            if ($card->account) {
                $this->accountSearch = "{$card->account->bank_name} - {$card->account->account_number} ({$card->account->customer?->full_name})";
            }

            $this->card_number = $card->card_number;
            $this->expiry_date = $card->expiry_date?->format('Y-m-d');
            $this->card_type = $card->card_type ?? '';
            $this->notes = $card->notes ?? "CVV :\nPIN :";
        } else {
            $this->reset(['account_id', 'card_number', 'expiry_date', 'card_type', 'notes', 'accountSearch']);
            $this->notes = "CVV :\nPIN :";
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'account_id', 'card_number', 'expiry_date', 'card_type', 'notes', 'accountSearch']);
        $this->isSearching = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'account_id' => $this->account_id,
            'card_number' => $this->card_number,
            'expiry_date' => $this->expiry_date ?: null,
            'card_type' => $this->card_type ?: null,
            'notes' => $this->notes,
        ];

        if ($this->editId) {
            Card::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Kartu berhasil diperbarui.');
        } else {
            Card::create($data);
            session()->flash('success', 'Kartu berhasil ditambahkan.');
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
            Card::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Kartu berhasil dihapus.');
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
        $this->viewingCard = Card::with(['account.customer'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingCard = null;
    }

    public function getRowsQuery()
    {
        return Card::query()
            ->with(['account.customer'])
            ->when($this->search, function ($query) {
                $query->where('card_number', 'like', '%'.$this->search.'%')
                    ->orWhere('card_type', 'like', '%'.$this->search.'%')
                    ->orWhereHas('account', function ($q) {
                        $q->where('account_number', 'like', '%'.$this->search.'%')
                            ->orWhere('bank_name', 'like', '%'.$this->search.'%');
                    });
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
        Card::whereIn('id', $this->selected)->delete();

        session()->flash('success', count($this->selected).' kartu berhasil dihapus.');

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
        return Excel::download(new CardsExport, 'cards.xlsx');
    }

    public function exportPdf()
    {
        $cards = Card::query()->with(['account.customer'])->latest()->get();
        $pdf = Pdf::loadView('exports.cards-pdf', ['cards' => $cards]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cards.pdf');
    }

    public function printDetailPdf(string $id)
    {
        $card = Card::with(['account.customer'])->findOrFail($id);
        $pdf = Pdf::loadView('exports.card-detail-pdf', ['card' => $card]);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'card_' . $card->card_number . '.pdf');
    }

    public function render()
    {
        $cards = $this->getRowsQuery()->paginate($this->perPage);

        // Logic pencarian akun untuk dropdown
        $searchedAccounts = [];
        if (strlen($this->accountSearch) >= 2) {
            $searchedAccounts = Account::query()
                ->with('customer')
                ->where('account_number', 'like', '%'.$this->accountSearch.'%')
                ->orWhere('bank_name', 'like', '%'.$this->accountSearch.'%')
                ->orWhereHas('customer', function ($q) {
                    $q->where('full_name', 'like', '%'.$this->accountSearch.'%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.rekening.card-crud', [
            'cards' => $cards,
            'searchedAccounts' => $searchedAccounts,
        ]);
    }
}
