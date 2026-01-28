<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Account::query()->with(['customer', 'agent'])->latest();
    }

    public function headings(): array
    {
        return [
            'Account Number',
            'Bank Name',
            'Branch',
            'Customer Name',
            'Customer NIK',
            'Agent Name',
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
            $account->account_number,
            $account->bank_name,
            $account->branch,
            $account->customer ? $account->customer->full_name : '',
            $account->customer ? $account->customer->nik : '',
            $account->agent ? $account->agent->agent_name : '',
            $account->opening_date ? $account->opening_date->format('Y-m-d') : '',
            $account->expired_on ? $account->expired_on->format('Y-m-d') : '',
            $account->status,
            $account->mobile_banking,
            $account->note,
            $account->created_at ? $account->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
