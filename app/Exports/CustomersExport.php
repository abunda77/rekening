<?php

namespace App\Exports;

use App\Services\CustomerListQuery;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Export the rows matching the filters applied on the customers page.
     */
    public function __construct(
        private readonly ?CustomerListQuery $customers = null,
    ) {}

    public function query()
    {
        return ($this->customers ?? new CustomerListQuery)->query();
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Full Name',
            'Mother Name',
            'Email',
            'Phone Number',
            'Province',
            'Regency',
            'District',
            'Village',
            'Address',
            'Note',
            'Created At',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->nik,
            $customer->full_name,
            $customer->mother_name,
            $customer->email,
            $customer->phone_number,
            $customer->province,
            $customer->regency,
            $customer->district,
            $customer->village,
            $customer->address,
            $customer->note,
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
