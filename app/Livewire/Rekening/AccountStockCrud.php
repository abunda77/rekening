<?php

namespace App\Livewire\Rekening;

use App\Imports\AccountStocksImport;
use App\Models\AccountStock;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app.sidebar')]
#[Title('Stok Rekening')]
class AccountStockCrud extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $filterInStock = '';

    public string $filterVip = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $account_number = '';

    public string $bank_name = '';

    public string $agent_id = '';

    public string $userName = '';

    public string $note = '';

    public bool $is_in_stock = true;

    public bool $is_vip = false;

    public $image; // Data image upload

    public bool $showModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    public ?AccountStock $viewingStock = null;

    // Bulk delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

    // Bulk upload
    public bool $showBulkUploadModal = false;

    public $bulkFile;

    public array $bulkImages = [];

    /** @var list<string> */
    public array $bulkSummary = [];

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
            'INA' => 'Bank Ina',
            'SAMPOERNA' => 'Bank Sampoerna',
            'NOBU' => 'Bank Nobu',
            'UOB' => 'Bank UOB',
            'SINARMAS' => 'Bank Sinarmas',
            'LAINNYA' => 'Lainnya',
        ];
    }

    protected function rules(): array
    {
        return [
            'account_number' => 'required|string|max:50|unique:account_stocks,account_number,'.$this->editId,
            'bank_name' => 'required|string|max:100',
            'agent_id' => 'nullable|exists:agents,id',
            'userName' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
            'is_in_stock' => 'boolean',
            'is_vip' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterInStock(): void
    {
        $this->resetPage();
    }

    public function updatedFilterVip(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $allowedFields = ['account_number', 'bank_name', 'is_in_stock', 'user', 'is_vip', 'created_at'];

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
            $stock = AccountStock::findOrFail($id);
            $this->account_number = $stock->account_number;
            $this->bank_name = $stock->bank_name;
            $this->agent_id = $stock->agent_id ?? '';
            $this->userName = $stock->user ?? '';
            $this->note = $stock->note ?? '';
            $this->is_in_stock = $stock->is_in_stock;
            $this->is_vip = $stock->is_vip;
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
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'agent_id' => $this->agent_id ?: null,
            'user' => $this->userName ?: null,
            'note' => $this->note ?: null,
            'is_in_stock' => $this->is_in_stock,
            'is_vip' => $this->is_vip,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('account-stocks', 'public');
        }

        if ($this->editId) {
            AccountStock::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Stok rekening berhasil diperbarui.');
        } else {
            AccountStock::create($data);
            session()->flash('success', 'Stok rekening berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function view(string $id): void
    {
        $this->viewingStock = AccountStock::with('agent')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingStock = null;
    }

    public function confirmDelete(string $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            AccountStock::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Stok rekening berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = $this->rowsQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
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
        AccountStock::query()->whereIn('id', $this->selected)->delete();

        session()->flash('success', count($this->selected).' stok rekening berhasil dihapus.');

        $this->selected = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function openBulkUploadModal(): void
    {
        $this->resetValidation();
        $this->reset(['bulkFile', 'bulkImages', 'bulkSummary']);
        $this->showBulkUploadModal = true;
    }

    public function closeBulkUploadModal(): void
    {
        $this->showBulkUploadModal = false;
        $this->reset(['bulkFile', 'bulkImages', 'bulkSummary']);
        $this->resetValidation();
    }

    public function bulkUpload(): void
    {
        $this->validate([
            'bulkFile' => 'nullable|file|extensions:xlsx,xls,csv|max:5120',
            'bulkImages.*' => 'image|max:2048',
        ]);

        if (! $this->bulkFile && empty($this->bulkImages)) {
            $this->addError('bulkFile', 'Pilih file data (Excel/CSV) atau gambar yang akan diunggah.');

            return;
        }

        $summary = [];

        if ($this->bulkFile) {
            try {
                $import = new AccountStocksImport;
                Excel::import($import, $this->bulkFile);

                $summary[] = "Data: {$import->created} baris baru, {$import->updated} baris diperbarui.";

                foreach ($import->notes as $note) {
                    $summary[] = $note;
                }
            } catch (\Throwable $exception) {
                report($exception);
                $this->addError('bulkFile', 'File data gagal diproses. Pastikan format kolom sesuai.');
            }
        }

        if (! empty($this->bulkImages)) {
            $matched = 0;
            $unmatched = [];

            foreach ($this->bulkImages as $uploadedImage) {
                $accountNumber = trim(pathinfo($uploadedImage->getClientOriginalName(), PATHINFO_FILENAME));
                $stock = AccountStock::query()->where('account_number', $accountNumber)->first();

                if (! $stock) {
                    $unmatched[] = $accountNumber;

                    continue;
                }

                $stock->update(['image' => $uploadedImage->store('account-stocks', 'public')]);
                $matched++;
            }

            $summary[] = "Gambar: {$matched} gambar terpasang ke no. rekening yang sesuai.";

            if (! empty($unmatched)) {
                $summary[] = 'Gambar tanpa no. rekening yang cocok: '.implode(', ', $unmatched).'.';
            }
        }

        $this->bulkSummary = $summary;
        $this->reset(['bulkFile', 'bulkImages']);
        $this->resetValidation();

        session()->flash('success', 'Bulk upload stok rekening selesai diproses.');
    }

    private function rowsQuery(): Builder
    {
        return AccountStock::query()
            ->with('agent')
            ->when($this->search, function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('account_number', 'like', '%'.$this->search.'%')
                        ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                        ->orWhere('user', 'like', '%'.$this->search.'%')
                        ->orWhereHas('agent', fn (Builder $agent) => $agent->where('agent_name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->filterInStock !== '', fn (Builder $query) => $query->where('is_in_stock', $this->filterInStock === 'yes'))
            ->when($this->filterVip !== '', fn (Builder $query) => $query->where('is_vip', $this->filterVip === 'yes'))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    private function resetForm(): void
    {
        $this->reset(['account_number', 'bank_name', 'agent_id', 'userName', 'note', 'image']);
        $this->is_in_stock = true;
        $this->is_vip = false;
    }

    public function render()
    {
        return view('livewire.rekening.account-stock-crud', [
            'stocks' => $this->rowsQuery()->paginate($this->perPage),
            'agents' => Agent::query()->orderBy('agent_name')->get(),
        ]);
    }
}
