<?php

namespace App\Livewire\Rekening;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\ImageOptimizer\OptimizerChainFactory;

#[Layout('layouts.app.sidebar')]
#[Title('Manajemen Customer')]
class CustomerCrud extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Form fields
    public ?string $editId = null;

    public string $nik = '';

    public string $full_name = '';

    public string $mother_name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $address = '';

    public $upload_ktp = null;

    public ?string $existing_ktp = null;

    public string $note = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // View Modal
    public bool $showViewModal = false;

    public ?Customer $viewingCustomer = null;

    protected function rules(): array
    {
        return [
            'nik' => 'required|string|size:16|unique:customers,nik,'.$this->editId,
            'full_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'upload_ktp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'note' => 'nullable|string',
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
        $this->upload_ktp = null;

        if ($id) {
            $customer = Customer::findOrFail($id);
            $this->nik = $customer->nik;
            $this->full_name = $customer->full_name;
            $this->mother_name = $customer->mother_name ?? '';
            $this->email = $customer->email ?? '';
            $this->phone_number = $customer->phone_number ?? '';
            $this->address = $customer->address ?? '';
            $this->existing_ktp = $customer->upload_ktp;
            $this->note = $customer->note ?? '';
        } else {
            $this->reset(['nik', 'full_name', 'mother_name', 'email', 'phone_number', 'address', 'existing_ktp', 'note']);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editId', 'nik', 'full_name', 'mother_name', 'email', 'phone_number', 'address', 'upload_ktp', 'existing_ktp', 'note']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nik' => $this->nik,
            'full_name' => $this->full_name,
            'mother_name' => $this->mother_name ?: null,
            'email' => $this->email ?: null,
            'phone_number' => $this->phone_number ?: null,
            'address' => $this->address ?: null,
            'note' => $this->note ?: null,
        ];

        if ($this->upload_ktp) {
            if ($this->editId && $this->existing_ktp) {
                Storage::disk('public')->delete($this->existing_ktp);
            }
            $data['upload_ktp'] = $this->upload_ktp->store('ktp', 'public');

            // Optimize uploaded image
            $fullPath = Storage::disk('public')->path($data['upload_ktp']);
            OptimizerChainFactory::create()->optimize($fullPath);
        }

        if ($this->editId) {
            Customer::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Customer berhasil diperbarui.');
        } else {
            Customer::create($data);
            session()->flash('success', 'Customer berhasil ditambahkan.');
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
            $customer = Customer::findOrFail($this->deleteId);
            if ($customer->upload_ktp) {
                Storage::disk('public')->delete($customer->upload_ktp);
            }
            $customer->delete();
            session()->flash('success', 'Customer berhasil dihapus.');
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
        $this->viewingCustomer = Customer::findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingCustomer = null;
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($query) {
                $query->where('nik', 'like', '%'.$this->search.'%')
                    ->orWhere('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone_number', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.rekening.customer-crud', [
            'customers' => $customers,
        ]);
    }
}
