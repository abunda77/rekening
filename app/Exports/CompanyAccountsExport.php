<?php

namespace App\Exports;

use App\Services\CompanyAccountListQuery;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanyAccountsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Export the rows matching the filters applied on the "Rekening PT" page.
     */
    public function __construct(
        private readonly ?CompanyAccountListQuery $companyAccounts = null,
    ) {}

    public function query()
    {
        return ($this->companyAccounts ?? new CompanyAccountListQuery)->query();
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Account Number',
            'Bank Name',
            'Branch',
            'Customer Name',
            'Agent Name',
            'Agent Supplier',
            'Opening Date',
            'Expired On',
            'Status',
            'Mobile Banking',
            'Note',
            'Created At',
        ];
    }

    public function map($account): array
    {
        return [
            $account->company_name,
            $account->account_number,
            $account->bank_name,
            $account->branch,
            $account->customer ? $account->customer->full_name : '',
            $account->agent ? $account->agent->agent_name : '',
            $account->agentSupplier ? $account->agentSupplier->name : '',
            $account->opening_date ? $account->opening_date->format('Y-m-d') : '',
            $account->expired_on ? $account->expired_on->format('Y-m-d') : '',
            $account->status,
            $account->mobile_banking,
            $account->note,
            $account->created_at ? $account->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
