<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Shared query for the cards table and its exports.
 *
 * The page and the exports both read from here so the exported file can never drift
 * from the rows on screen (search box and sort order).
 */
class CardListQuery
{
    public const SORTABLE = [
        'card_number' => 'card_number',
        'card_type' => 'card_type',
        'expiry_date' => 'expiry_date',
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
        return Card::query()
            ->with(['account.customer'])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('card_number', 'like', '%'.$this->search.'%')
                        ->orWhere('card_type', 'like', '%'.$this->search.'%')
                        ->orWhereHas('account', function (Builder $query): void {
                            $query->where('account_number', 'like', '%'.$this->search.'%')
                                ->orWhere('bank_name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy(self::normalizeSort($this->sort), self::normalizeDirection($this->direction));
    }

    /**
     * Every row matching the filters (used by the exports, which are not paginated).
     *
     * @return Collection<int, Card>
     */
    public function rows(): Collection
    {
        return $this->query()->get();
    }
}
