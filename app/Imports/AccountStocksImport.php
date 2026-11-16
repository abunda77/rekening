<?php

namespace App\Imports;

use App\Models\AccountStock;
use App\Models\Agent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Bulk import stok rekening from a spreadsheet (xlsx/xls/csv).
 *
 * Recognised headings (case insensitive, separator agnostic):
 * No. Rek | Bank | Agent | In Stock | User | VIP
 */
class AccountStocksImport implements ToCollection, WithHeadingRow
{
    public int $created = 0;

    public int $updated = 0;

    /** @var list<string> */
    public array $notes = [];

    /** @var array<string, string> */
    private array $agentsByCode = [];

    /** @var array<string, string> */
    private array $agentsByName = [];

    public function __construct()
    {
        Agent::query()->get(['id', 'agent_code', 'agent_name'])->each(function (Agent $agent): void {
            if ($agent->agent_code) {
                $this->agentsByCode[strtoupper((string) $agent->agent_code)] = (string) $agent->id;
            }

            if ($agent->agent_name) {
                $this->agentsByName[strtolower((string) $agent->agent_name)] = (string) $agent->id;
            }
        });
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            /** @var Collection<string, mixed> $row */
            $row = collect($row)->map(fn ($value) => is_string($value) ? trim($value) : $value);

            $accountNumber = $this->firstValue($row, [
                'no_rek', 'no_rekening', 'nomor_rekening', 'account_number', 'rekening',
            ]);

            if ($accountNumber === null) {
                $this->notes[] = 'Baris '.($index + 2).' dilewati: no. rekening kosong.';

                continue;
            }

            $agentLabel = (string) $this->firstValue($row, ['agent', 'agent_code', 'kode_agent', 'user_agent']);
            $agentId = $this->resolveAgentId($agentLabel);

            if ($agentLabel !== '' && $agentId === null) {
                $this->notes[] = 'Baris '.($index + 2).': agent "'.$agentLabel.'" tidak ditemukan.';
            }

            $bankName = (string) $this->firstValue($row, ['bank', 'bank_name', 'nama_bank']);

            $stock = AccountStock::query()->firstOrNew(['account_number' => (string) $accountNumber]);

            $stock->fill([
                'bank_name' => $bankName !== '' ? strtoupper($bankName) : 'LAINNYA',
                'agent_id' => $agentId,
                'is_in_stock' => $this->parseBoolean($this->firstValue($row, ['in_stock', 'instock', 'stock']), true),
                'user' => $this->firstValue($row, ['user', 'user_name', 'username']),
                'is_vip' => $this->parseBoolean($this->firstValue($row, ['vip', 'is_vip']), false),
            ]);

            $isNew = ! $stock->exists;
            $stock->save();

            $isNew ? $this->created++ : $this->updated++;
        }
    }

    /**
     * @param  Collection<string, mixed>  $row
     * @param  list<string>  $keys
     */
    private function firstValue(Collection $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $row->get($key);

            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function resolveAgentId(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        return $this->agentsByCode[strtoupper($value)]
            ?? $this->agentsByName[strtolower($value)]
            ?? null;
    }

    private function parseBoolean(?string $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = strtolower($value);

        if (in_array($normalized, ['1', 'yes', 'y', 'ya', 'true', 'in stock', 'instock', 'vip'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'no', 'n', 'tidak', 'false', 'out of stock', 'outofstock', 'non vip', 'non-vip'], true)) {
            return false;
        }

        return $default;
    }
}
