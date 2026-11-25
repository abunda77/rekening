<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Services\CardListQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CardPdfExportController extends Controller
{
    /**
     * Column labels of the cards PDF, in display order.
     *
     * @var list<string>
     */
    private const HEADERS = [
        'No. Kartu',
        'Tipe',
        'Bank',
        'No. Rekening',
        'Nasabah',
        'Expired',
    ];

    /**
     * Download the cards that match the filters currently applied on the cards page.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $cardQuery = CardListQuery::fromRequest($request);

        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $rows = $cardQuery->rows()
            ->map(fn (Card $card): array => [
                $card->card_number,
                $card->card_type ?: '-',
                $card->account?->bank_name ?: '-',
                $card->account?->account_number ?: '-',
                $card->account?->customer?->full_name ?: '-',
                $card->expiry_date?->format('m/Y') ?: '-',
            ])
            ->all();

        $meta = array_values(array_filter([
            'Total: '.count($rows).' kartu',
            $cardQuery->search() === '' ? '' : 'Pencarian: "'.$cardQuery->search().'"',
            'Dicetak: '.now()->format('d M Y H:i'),
        ]));

        $pdf = Pdf::loadView('exports.list-pdf', [
            'title' => 'Data Kartu ATM',
            'headers' => self::HEADERS,
            'rows' => $rows,
            'meta' => $meta,
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'cards.pdf', ['Content-Type' => 'application/pdf']);
    }
}
