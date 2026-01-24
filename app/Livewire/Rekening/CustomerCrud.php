<?php

namespace App\Livewire\Rekening;

use App\Exports\CustomersExport;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
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

    // Region Data
    public array $provinces = [];

    public array $regencies = [];

    public array $districts = [];

    public array $villages = [];

    // Selected Regions
    public ?string $province_code = null;

    public ?string $province_name = null;

    public ?string $regency_code = null;

    public ?string $regency_name = null;

    public ?string $district_code = null;

    public ?string $district_name = null;

    public ?string $village_code = null;

    public ?string $village_name = null;

    public $upload_ktp = null;

    public ?string $existing_ktp = null;

    public string $note = '';

    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?string $deleteId = null;

    // Bulk Delete
    public array $selected = [];

    public bool $selectAll = false;

    public bool $showBulkDeleteModal = false;

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
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'regency_code' => 'nullable|string',
            'regency_name' => 'nullable|string',
            'district_code' => 'nullable|string',
            'district_name' => 'nullable|string',
            'village_code' => 'nullable|string',
            'village_name' => 'nullable|string',
            'upload_ktp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'note' => 'nullable|string',
        ];
    }

    public function updatedProvinceCode($value): void
    {
        $this->province_name = collect($this->provinces)->firstWhere('code', $value)['name'] ?? null;
        $this->reset(['regency_code', 'regency_name', 'district_code', 'district_name', 'village_code', 'village_name', 'regencies', 'districts', 'villages']);
        if ($value) {
            $this->fetchRegencies();
        }
    }

    public function updatedRegencyCode($value): void
    {
        $this->regency_name = collect($this->regencies)->firstWhere('code', $value)['name'] ?? null;
        $this->reset(['district_code', 'district_name', 'village_code', 'village_name', 'districts', 'villages']);
        if ($value) {
            $this->fetchDistricts();
        }
    }

    public function updatedDistrictCode($value): void
    {
        $this->district_name = collect($this->districts)->firstWhere('code', $value)['name'] ?? null;
        $this->reset(['village_code', 'village_name', 'villages']);
        if ($value) {
            $this->fetchVillages();
        }
    }

    public function updatedVillageCode($value): void
    {
        $this->village_name = collect($this->villages)->firstWhere('code', $value)['name'] ?? null;
    }

    protected function fetchRegionData(string $endpoint)
    {
        $apiKey = env('API_CO_ID', 'API_CO_ID'); // Fallback if needed, but user should provide it

        try {
            $response = Http::withHeaders([
                'x-api-co-id' => $apiKey,
            ])->get("https://use.api.co.id{$endpoint}");

            if ($response->successful() && ($response->json()['is_success'] ?? false)) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // connection error or otherwise
        }

        return [];
    }

    public function fetchProvinces(): void
    {
        $this->provinces = $this->fetchRegionData('/regional/indonesia/provinces');
    }

    public function fetchRegencies(): void
    {
        if ($this->province_code) {
            $this->regencies = $this->fetchRegionData("/regional/indonesia/provinces/{$this->province_code}/regencies");
        }
    }

    public function fetchDistricts(): void
    {
        if ($this->regency_code) {
            // Using the pattern likely supported or implied
            // Try regencies/{code}/districts first, if fails we might need to adjust.
            // Based on hierarchy, it should be regencies/{code}/districts.
            $this->districts = $this->fetchRegionData("/regional/indonesia/regencies/{$this->regency_code}/districts");

            // Fallback if the above returns empty? The user snippet showed /regional/indonesia/districts
            // If the above is empty, maybe try filtering listing?
            if (empty($this->districts)) {
                // Try alternate endpoint if the first one assumes a pattern not strictly documented
                $this->districts = $this->fetchRegionData("/regional/indonesia/districts?regency_code={$this->regency_code}");
            }
        }
    }

    public function fetchVillages(): void
    {
        if ($this->district_code) {
            $this->villages = $this->fetchRegionData("/regional/indonesia/districts/{$this->district_code}/villages");
        }
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
            $this->province_code = $customer->province_code;
            $this->province_name = $customer->province;
            $this->regency_code = $customer->regency_code;
            $this->regency_name = $customer->regency;
            $this->district_code = $customer->district_code;
            $this->district_name = $customer->district;
            $this->village_code = $customer->village_code;
            $this->village_name = $customer->village;

            // Load dependent data if codes exist
            if ($this->province_code) {
                $this->fetchRegencies();
            }
            if ($this->regency_code) {
                $this->fetchDistricts();
            }
            if ($this->district_code) {
                $this->fetchVillages();
            }

            $this->existing_ktp = $customer->upload_ktp;
            $this->note = $customer->note ?? '';
        } else {
            $this->reset([
                'nik', 'full_name', 'mother_name', 'email', 'phone_number', 'address',
                'province_code', 'province_name', 'regency_code', 'regency_name',
                'district_code', 'district_name', 'village_code', 'village_name',
                'existing_ktp', 'note',
            ]);
            $this->resetValidation();
        }

        if (empty($this->provinces)) {
            $this->fetchProvinces();
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showModal = false;
        $this->reset([
            'editId', 'nik', 'full_name', 'mother_name', 'email', 'phone_number', 'address',
            'province_code', 'province_name', 'regency_code', 'regency_name',
            'district_code', 'district_name', 'village_code', 'village_name',
            'upload_ktp', 'existing_ktp', 'note',
            'regencies', 'districts', 'villages',
        ]);
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
            'phone_number' => $this->phone_number ?: null,
            'address' => $this->address ?: null,
            'province_code' => $this->province_code ?: null,
            'province' => $this->province_name ?: null,
            'regency_code' => $this->regency_code ?: null,
            'regency' => $this->regency_name ?: null,
            'district_code' => $this->district_code ?: null,
            'district' => $this->district_name ?: null,
            'village_code' => $this->village_code ?: null,
            'village' => $this->village_name ?: null,
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

    public function getCustomersQuery()
    {
        return Customer::query()
            ->when($this->search, function ($query) {
                $query->where('nik', 'like', '%'.$this->search.'%')
                    ->orWhere('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone_number', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getCustomersQuery()->paginate($this->perPage)->pluck('id')->map(fn ($id) => (string) $id)->toArray();
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
        $customers = Customer::whereIn('id', $this->selected)->get();
        foreach ($customers as $customer) {
            if ($customer->upload_ktp) {
                Storage::disk('public')->delete($customer->upload_ktp);
            }
            $customer->delete();
        }

        session()->flash('success', count($this->selected).' customer berhasil dihapus.');

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
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }

    public function exportPdf()
    {
        $customers = Customer::query()->latest()->get();
        $pdf = Pdf::loadView('exports.customers-pdf', ['customers' => $customers]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'customers.pdf');
    }

    public function printDetailPdf(string $id)
    {
        $customer = Customer::findOrFail($id);
        $pdf = Pdf::loadView('exports.customer-detail-pdf', ['customer' => $customer]);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'customer_'.$customer->nik.'.pdf');
    }

    public function render()
    {
        $customers = $this->getCustomersQuery()->paginate($this->perPage);

        return view('livewire.rekening.customer-crud', [
            'customers' => $customers,
        ]);
    }
}
