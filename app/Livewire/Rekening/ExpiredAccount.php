<?php

namespace App\Livewire\Rekening;

use App\Services\ExpiredAccountQuery;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app.sidebar')]
#[Title('Rekening Expired')]
class ExpiredAccount extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'jenis', except: '')]
    public string $filterType = '';

    #[Url(as: 'periode', except: 'this-month')]
    public string $filterPeriod = 'this-month';

    #[Url(as: 'status', except: '')]
    public string $filterStatus = '';

    public string $sortField = ExpiredAccountQuery::DEFAULT_SORT;

    public string $sortDirection = 'asc';

    public int $perPage = 25;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'filterType', 'filterPeriod', 'filterStatus', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        if (! array_key_exists($field, ExpiredAccountQuery::SORTABLE)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterType', 'filterStatus']);
        $this->filterPeriod = 'this-month';
        $this->resetPage();
    }

    public function exportUrl(): string
    {
        return route('rekening.expired-accounts.export', array_filter([
            'type' => $this->filterType,
            'period' => $this->filterPeriod,
            'status' => $this->filterStatus,
            'search' => trim($this->search),
            'sort' => $this->sortField,
            'direction' => $this->sortDirection,
        ], fn (string $value) => $value !== ''));
    }

    public function render()
    {
        $this->normalizeFilters();

        $query = new ExpiredAccountQuery(
            $this->filterType,
            $this->filterPeriod,
            $this->filterStatus,
            trim($this->search),
        );

        return view('livewire.rekening.expired-account', [
            'accounts' => $query->paginate($this->perPage, $this->sortField, $this->sortDirection),
            'totals' => ExpiredAccountQuery::totals(),
            'exportUrl' => $this->exportUrl(),
        ]);
    }

    private function normalizeFilters(): void
    {
        if (! ExpiredAccountQuery::isValidType($this->filterType)) {
            $this->filterType = '';
        }

        if (! ExpiredAccountQuery::isValidPeriod($this->filterPeriod)) {
            $this->filterPeriod = 'this-month';
        }

        if (! ExpiredAccountQuery::isValidStatus($this->filterStatus)) {
            $this->filterStatus = '';
        }

        if (! in_array($this->perPage, [10, 25, 50, 100], true)) {
            $this->perPage = 25;
        }
    }
}
