<?php

namespace App\Http\Controllers;

use App\Models\CompanyAccount;
use App\Services\CompanyAccountListQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyAccountPdfExportController extends Controller
{
    /**
     * Column labels of the "Rekening PT" PDF, in display order.
     *
     * @var list<string>
     */
    private const HEADERS = [
        'Nama PT',
        'No. Rekening',
        'Bank',
        'Cabang',
        'Nasabah',
        'User',
        'Agent Supplier',
        'Tgl Buka',
        'Tgl Berakhir',
        'Status',
    ];

    /**
     * Download the company accounts that match the filters currently applied on the page.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $companyAccountQuery = CompanyAccountListQuery::fromRequest($request);

        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $rows = $companyAccountQuery->rows()
            ->map(fn (CompanyAccount $account): array => [
                $account->company_name ?: '-',
                $account->account_number ?: '-',
                $account->bank_name ?: '-',
                $account->branch ?: '-',
                $account->customer?->full_name ?: '-',
                $account->agent?->agent_name ?: '-',
                $account->agentSupplier?->name ?: '-',
                $account->opening_date?->format('d M Y') ?: '-',
                $account->expired_on?->format('d M Y') ?: '-',
                ucfirst((string) $account->status),
            ])
            ->all();

        $meta = array_values(array_filter([
            'Total: '.count($rows).' rekening PT',
            $companyAccountQuery->status() === '' ? '' : 'Status: '.CompanyAccountListQuery::STATUSES[$companyAccountQuery->status()],
            $companyAccountQuery->agentName() === '' ? '' : 'User: '.$companyAccountQuery->agentName(),
            $companyAccountQuery->agentSupplierName() === '' ? '' : 'Agent Supplier: '.$companyAccountQuery->agentSupplierName(),
            $companyAccountQuery->search() === '' ? '' : 'Pencarian: "'.$companyAccountQuery->search().'"',
            'Dicetak: '.now()->format('d M Y H:i'),
        ]));

        $pdf = Pdf::loadView('exports.list-pdf', [
            'title' => 'Data Rekening PT',
            'headers' => self::HEADERS,
            'rows' => $rows,
            'meta' => $meta,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'company-accounts.pdf', ['Content-Type' => 'application/pdf']);
    }
}
