<?php

namespace App\Livewire\Rekening;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Activity Log')]
class ActivityLogCrud extends Component
{
    use WithPagination;

    /**
     * Columns the table may be sorted by.
     */
    private const SORTABLE = ['created_at', 'status_code', 'duration_ms', 'method', 'ip_address'];

    public string $search = '';

    public string $filterMethod = '';

    public string $filterStatus = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 15;

    public bool $showViewModal = false;

    public ?ActivityLog $viewingLog = null;

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMethod(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function view(int $id): void
    {
        $this->authorizeAccess();

        $this->viewingLog = ActivityLog::findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewingLog = null;
    }

    /**
     * Rows of the activity table, filtered and sorted.
     */
    public function getRowsQuery()
    {
        return ActivityLog::query()
            ->search($this->search)
            ->statusClass($this->filterStatus)
            ->when($this->filterMethod, fn ($query) => $query->where('method', $this->filterMethod))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id');
    }

    /**
     * Available HTTP methods for the filter dropdown.
     *
     * @return list<string>
     */
    public function methods(): array
    {
        return ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    }

    /**
     * Guard the screen and its actions; route middleware does not cover
     * Livewire action requests.
     */
    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->hasRole('Super Admin'), 403);
    }

    public function render()
    {
        return view('livewire.rekening.activity-log-crud', [
            'logs' => $this->getRowsQuery()->paginate($this->perPage),
            'methods' => $this->methods(),
        ]);
    }
}
