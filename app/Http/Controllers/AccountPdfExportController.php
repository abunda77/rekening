<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountListQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountPdfExportController extends Controller
{
    /**
     * Column labels of the accounts PDF, in display order.
     *
     * @var list<string>
     */
    private const HEADERS = [
        'No. Rekening',
        'Bank',
        'Cabang',
        'Nasabah',
        'NIK',
        'Status',
        'Tgl Buka',
        'Tgl Berakhir',
    ];

    /**
     * Download the accounts that match the filters currently applied on the accounts page.
     *
     * Served from a plain GET route so the browser handles it as a normal download;
     * routing it through a Livewire action returned the whole document base64 encoded
     * inside the JSON response, which fails silently for large exports.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $accountQuery = AccountListQuery::fromRequest($request);

        // The document holds every matching row, so give dompdf headroom when the
        // dataset is big (the row rendering itself is deliberately lightweight).
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $rows = $accountQuery->rows()
            ->map(fn (Account $account): array => [
                $account->account_number,
                $account->bank_name,
                $account->branch ?: '-',
                $account->customer?->full_name ?: '-',
                $account->customer?->nik ?: '-',
                ucfirst((string) $account->status),
                $account->opening_date?->format('d M Y') ?: '-',
                $account->expired_on?->format('d M Y') ?: '-',
            ])
            ->all();

        $meta = array_values(array_filter([
            'Total: '.count($rows).' rekening',
            $accountQuery->status() === '' ? '' : 'Status: '.AccountListQuery::STATUSES[$accountQuery->status()],
            $accountQuery->agentName() === '' ? '' : 'User: '.$accountQuery->agentName(),
            $accountQuery->agentSupplierName() === '' ? '' : 'Agent Supplier: '.$accountQuery->agentSupplierName(),
            $accountQuery->search() === '' ? '' : 'Pencarian: "'.$accountQuery->search().'"',
            'Dicetak: '.now()->format('d M Y H:i'),
        ]));

        $pdf = Pdf::loadView('exports.list-pdf', [
            'title' => 'Data Rekening',
            'headers' => self::HEADERS,
            'rows' => $rows,
            'meta' => $meta,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'accounts.pdf', ['Content-Type' => 'application/pdf']);
    }
}
