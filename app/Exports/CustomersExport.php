<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Customer::query()->latest();
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
