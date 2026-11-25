<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerListQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerPdfExportController extends Controller
{
    /**
     * Column labels of the customers PDF, in display order.
     *
     * @var list<string>
     */
    private const HEADERS = [
        'NIK',
        'Nama Lengkap',
        'Ibu Kandung',
        'Email',
        'Telepon',
        'Wilayah',
        'Alamat',
    ];

    /**
     * Download the customers that match the filters currently applied on the customers page.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $customerQuery = CustomerListQuery::fromRequest($request);

        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $rows = $customerQuery->rows()
            ->map(fn (Customer $customer): array => [
                $customer->nik ?: '-',
                $customer->full_name ?: '-',
                $customer->mother_name ?: '-',
                $customer->email ?: '-',
                $customer->phone_number ?: '-',
                collect([$customer->village, $customer->district, $customer->regency, $customer->province])
                    ->filter()
                    ->implode(', ') ?: '-',
                $customer->address ?: '-',
            ])
            ->all();

        $meta = array_values(array_filter([
            'Total: '.count($rows).' nasabah',
            $customerQuery->search() === '' ? '' : 'Pencarian: "'.$customerQuery->search().'"',
            'Dicetak: '.now()->format('d M Y H:i'),
        ]));

        $pdf = Pdf::loadView('exports.list-pdf', [
            'title' => 'Data Nasabah',
            'headers' => self::HEADERS,
            'rows' => $rows,
            'meta' => $meta,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'customers.pdf', ['Content-Type' => 'application/pdf']);
    }
}
