<?php

namespace App\Livewire\Rekening;

use App\Models\Account;
use App\Models\Card;
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

    public string $cvv = '';

    public ?string $expiry_date = null;

    public string $pin_hash = '';

    public string $card_type = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    protected function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'card_number' => 'required|string|max:20|unique:cards,card_number,'.$this->editId,
            'cvv' => $this->editId ? 'nullable|string|size:3' : 'required|string|size:3',
            'expiry_date' => 'nullable|date',
            'pin_hash' => $this->editId ? 'nullable|string|min:4|max:6' : 'required|string|min:4|max:6',
            'card_type' => 'nullable|string|max:50',
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
            $card = Card::findOrFail($id);
            $this->account_id = $card->account_id;
            $this->card_number = $card->card_number;
            $this->cvv = '';
            $this->expiry_date = $card->expiry_date?->format('Y-m-d');
            $this->pin_hash = '';
            $this->card_type = $card->card_type ?? '';
        } else {
            $this->reset(['account_id', 'card_number', 'cvv', 'expiry_date', 'pin_hash', 'card_type']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'account_id', 'card_number', 'cvv', 'expiry_date', 'pin_hash', 'card_type']);
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
        ];

        if ($this->cvv) {
            $data['cvv'] = $this->cvv;
        }

        if ($this->pin_hash) {
            $data['pin_hash'] = $this->pin_hash;
        }

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

    public function render()
    {
        $cards = Card::query()
            ->with(['account.customer'])
            ->when($this->search, function ($query) {
                $query->where('card_number', 'like', '%'.$this->search.'%')
                    ->orWhere('card_type', 'like', '%'.$this->search.'%')
                    ->orWhereHas('account', function ($q) {
                        $q->where('account_number', 'like', '%'.$this->search.'%')
                            ->orWhere('bank_name', 'like', '%'.$this->search.'%');
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.rekening.card-crud', [
            'cards' => $cards,
            'accounts' => Account::with('customer')->orderBy('account_number')->get(),
        ]);
    }
}
