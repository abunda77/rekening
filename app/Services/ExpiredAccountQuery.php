<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Shared query for every table on the "Rekening Expired" page.
 *
 * The page and its PDF export both read from here so the exported file can never
 * drift from the rows shown on screen.
 */
class ExpiredAccountQuery
{
    public const TYPES = [
        'bank' => 'Rekening Bank',
        'pt' => 'Rekening PT',
    ];

    public const PERIODS = [
        'this-month' => 'Bulan Ini',
        'next-month' => 'Bulan Depan',
    ];

    public const STATUSES = [
        'aktif' => 'Aktif',
        'bermasalah' => 'Bermasalah',
        'nonaktif' => 'Non-Aktif',
    ];

    public const SORTABLE = [
        'expired_on' => 'expired_on',
        'account_number' => 'account_number',
        'bank_name' => 'bank_name',
        'customer_name' => 'customer_name',
        'agent_name' => 'agent_name',
        'company_name' => 'company_name',
        'status' => 'status',
    ];

    public const DEFAULT_SORT = 'expired_on';

    public function __construct(
        private readonly string $type = '',
        private readonly string $period = 'this-month',
        private readonly string $status = '',
        private readonly string $search = '',
    ) {}

    public static function isValidType(string $type): bool
    {
        return $type === '' || isset(self::TYPES[$type]);
    }

    public static function isValidPeriod(string $period): bool
    {
        return isset(self::PERIODS[$period]);
    }

    public static function isValidStatus(string $status): bool
    {
        return $status === '' || isset(self::STATUSES[$status]);
    }

    /**
     * Resolve the start and end date of the requested period.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public static function periodRange(string $period): array
    {
        // Starting from the first day keeps "next month" safe on the 29th-31st.
        $start = $period === 'next-month'
            ? now()->startOfMonth()->addMonth()
            : now()->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * Total accounts per type and period, ignoring the active filters.
     *
     * @return array{bank_this_month: int, bank_next_month: int, pt_this_month: int, pt_next_month: int}
     */
    public static function totals(): array
    {
        return [
            'bank_this_month' => self::countFor('accounts', 'this-month'),
            'bank_next_month' => self::countFor('accounts', 'next-month'),
            'pt_this_month' => self::countFor('company_accounts', 'this-month'),
            'pt_next_month' => self::countFor('company_accounts', 'next-month'),
        ];
    }

    public function paginate(int $perPage, string $sortField, string $sortDirection): LengthAwarePaginator
    {
        return $this->sorted($sortField, $sortDirection)->paginate($perPage);
    }

    public function get(string $sortField, string $sortDirection): Collection
    {
        return $this->sorted($sortField, $sortDirection)->get();
    }

    public function title(): string
    {
        $type = self::TYPES[$this->type] ?? 'Semua Rekening';
        $period = self::PERIODS[$this->period] ?? self::PERIODS['this-month'];

        return "Rekening Expired — {$type} — {$period}";
    }

    public function filename(): string
    {
        return 'rekening-expired-'.($this->type ?: 'semua').'-'.$this->period.'.pdf';
    }

    private static function countFor(string $table, string $period): int
    {
        [$start, $end] = self::periodRange($period);

        return DB::table($table)->whereBetween('expired_on', [$start, $end])->count();
    }

    /**
     * Build the filtered query, union-ing both account types when no type is selected.
     */
    private function query(): Builder
    {
        [$start, $end] = self::periodRange($this->period);

        $bank = $this->filtered($this->bankQuery(), 'accounts', $start, $end);
        $company = $this->filtered($this->companyAccountQuery(), 'company_accounts', $start, $end);

        return match ($this->type) {
            'bank' => $bank,
            'pt' => $company,
            default => $bank->unionAll($company),
        };
    }

    private function sorted(string $sortField, string $sortDirection): Builder
    {
        $column = self::SORTABLE[$sortField] ?? self::SORTABLE[self::DEFAULT_SORT];
        $direction = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        return $this->query()->orderBy($column, $direction);
    }

    private function filtered(Builder $query, string $table, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query
            ->whereBetween($table.'.expired_on', [$start, $end])
            ->when($this->status !== '', fn (Builder $query) => $query->where($table.'.status', $this->status))
            ->when($this->search !== '', function (Builder $query) use ($table) {
                $term = '%'.$this->search.'%';

                $query->where(function (Builder $query) use ($table, $term) {
                    $query->where($table.'.account_number', 'like', $term)
                        ->orWhere($table.'.bank_name', 'like', $term)
                        ->orWhere('customers.full_name', 'like', $term)
                        ->orWhere('agents.agent_name', 'like', $term);

                    if ($table === 'company_accounts') {
                        $query->orWhere('company_accounts.company_name', 'like', $term);
                    }
                });
            });
    }

    private function bankQuery(): Builder
    {
        return DB::table('accounts')
            ->leftJoin('customers', 'customers.id', '=', 'accounts.customer_id')
            ->leftJoin('agents', 'agents.id', '=', 'accounts.agent_id')
            ->select([
                DB::raw("'bank' as type"),
                'accounts.id',
                'accounts.account_number',
                'accounts.bank_name',
                'accounts.expired_on',
                'accounts.status',
                'customers.full_name as customer_name',
                'agents.agent_name as agent_name',
                DB::raw('null as company_name'),
            ]);
    }

    private function companyAccountQuery(): Builder
    {
        return DB::table('company_accounts')
            ->leftJoin('customers', 'customers.id', '=', 'company_accounts.customer_id')
            ->leftJoin('agents', 'agents.id', '=', 'company_accounts.agent_id')
            ->select([
                DB::raw("'pt' as type"),
                'company_accounts.id',
                'company_accounts.account_number',
                'company_accounts.bank_name',
                'company_accounts.expired_on',
                'company_accounts.status',
                'customers.full_name as customer_name',
                'agents.agent_name as agent_name',
                'company_accounts.company_name',
            ]);
    }
}
