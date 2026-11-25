<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Shared query for the customers table and its exports.
 *
 * The page and the exports both read from here so the exported file can never drift
 * from the rows on screen (search box and sort order).
 */
class CustomerListQuery
{
    public const SORTABLE = [
        'nik' => 'nik',
        'full_name' => 'full_name',
        'created_at' => 'created_at',
    ];

    public const DEFAULT_SORT = 'created_at';

    public const DEFAULT_DIRECTION = 'desc';

    public function __construct(
        private readonly string $search = '',
        private readonly string $sort = self::DEFAULT_SORT,
        private readonly string $direction = self::DEFAULT_DIRECTION,
    ) {}

    public static function normalizeSort(string $sort): string
    {
        return array_key_exists($sort, self::SORTABLE) ? $sort : self::DEFAULT_SORT;
    }

    public static function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : self::DEFAULT_DIRECTION;
    }

    /**
     * Build the query from the filters of an export request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            search: trim((string) $request->query('search', '')),
            sort: (string) $request->query('sort', self::DEFAULT_SORT),
            direction: (string) $request->query('direction', self::DEFAULT_DIRECTION),
        );
    }

    public function search(): string
    {
        return $this->search;
    }

    public function query(): Builder
    {
        return Customer::query()
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('nik', 'like', '%'.$this->search.'%')
                        ->orWhere('full_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone_number', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy(self::normalizeSort($this->sort), self::normalizeDirection($this->direction));
    }

    /**
     * Every row matching the filters (used by the exports, which are not paginated).
     *
     * @return Collection<int, Customer>
     */
    public function rows(): Collection
    {
        return $this->query()->get();
    }
}
