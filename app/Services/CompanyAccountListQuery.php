<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentSupplier;
use App\Models\CompanyAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Shared query for the "Rekening PT" table and its exports.
 *
 * The page and the exports both read from here so the exported file can never drift
 * from the rows on screen (search box, status/user/agent-supplier filters and sort).
 */
class CompanyAccountListQuery
{
    public const STATUSES = AccountListQuery::STATUSES;

    public const SORTABLE = [
        'company_name' => 'company_name',
        'account_number' => 'account_number',
        'bank_name' => 'bank_name',
        'agent_name' => 'agent_name',
        'agent_supplier_name' => 'agent_supplier_name',
        'status' => 'status',
        'opening_date' => 'opening_date',
        'expired_on' => 'expired_on',
        'created_at' => 'created_at',
    ];

    public const DEFAULT_SORT = 'created_at';

    public const DEFAULT_DIRECTION = 'desc';

    public function __construct(
        private readonly string $search = '',
        private readonly string $status = '',
        private readonly string $agentId = '',
        private readonly string $agentSupplierId = '',
        private readonly string $sort = self::DEFAULT_SORT,
        private readonly string $direction = self::DEFAULT_DIRECTION,
    ) {}

    public static function isValidStatus(string $status): bool
    {
        return $status === '' || array_key_exists($status, self::STATUSES);
    }

    /**
     * Build the query from the filters of an export request, rejecting unknown statuses.
     */
    public static function fromRequest(Request $request): self
    {
        $status = (string) $request->query('status', '');

        abort_unless(self::isValidStatus($status), 404);

        return new self(
            search: trim((string) $request->query('search', '')),
            status: $status,
            agentId: (string) $request->query('agent', ''),
            agentSupplierId: (string) $request->query('agent_supplier', ''),
            sort: (string) $request->query('sort', self::DEFAULT_SORT),
            direction: (string) $request->query('direction', self::DEFAULT_DIRECTION),
        );
    }

    public static function normalizeSort(string $sort): string
    {
        return array_key_exists($sort, self::SORTABLE) ? $sort : self::DEFAULT_SORT;
    }

    public static function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : self::DEFAULT_DIRECTION;
    }

    public function search(): string
    {
        return $this->search;
    }

    public function status(): string
    {
        return $this->status;
    }

    /**
     * Display name of the selected User filter, empty when no filter is applied.
     */
    public function agentName(): string
    {
        return $this->agentId === '' ? '' : (string) Agent::whereKey($this->agentId)->value('agent_name');
    }

    /**
     * Display name of the selected Agent Supplier filter, empty when no filter is applied.
     */
    public function agentSupplierName(): string
    {
        return $this->agentSupplierId === '' ? '' : (string) AgentSupplier::whereKey($this->agentSupplierId)->value('name');
    }

    public function query(): Builder
    {
        return CompanyAccount::query()
            ->with(['customer', 'agent', 'agentSupplier'])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('company_name', 'like', '%'.$this->search.'%')
                        ->orWhere('account_number', 'like', '%'.$this->search.'%')
                        ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                        ->orWhere('note', 'like', '%'.$this->search.'%')
                        // Mobile banking hanya dicari dengan kata/frasa, bukan angka (PIN dan sejenisnya).
                        ->when(preg_match('/\\p{L}/u', $this->search) === 1, function (Builder $query): void {
                            $query->orWhere('mobile_banking', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('customer', fn (Builder $customer) => $customer->where('full_name', 'like', '%'.$this->search.'%'))
                        ->orWhereHas('agentSupplier', function (Builder $supplier): void {
                            $supplier->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('contact_number', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when($this->agentId !== '', fn (Builder $query) => $query->where('agent_id', $this->agentId))
            ->when($this->agentSupplierId !== '', fn (Builder $query) => $query->where('agent_supplier_id', $this->agentSupplierId))
            ->orderBy($this->sortColumn(), self::normalizeDirection($this->direction));
    }

    /**
     * Every row matching the filters (used by the exports, which are not paginated).
     *
     * @return Collection<int, CompanyAccount>
     */
    public function rows(): Collection
    {
        return $this->query()->get();
    }

    /**
     * Resolve the column used for sorting, including related User and Agent Supplier columns.
     */
    private function sortColumn(): string|Builder
    {
        return match ($this->sort) {
            'agent_name' => Agent::query()->select('agent_name')->whereColumn('agents.id', 'company_accounts.agent_id'),
            'agent_supplier_name' => AgentSupplier::query()->select('name')->whereColumn('agent_suppliers.id', 'company_accounts.agent_supplier_id'),
            default => self::normalizeSort($this->sort),
        };
    }
}
