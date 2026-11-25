<?php

namespace App\Exports;

use App\Services\CardListQuery;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CardsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Export the rows matching the filters applied on the cards page.
     */
    public function __construct(
        private readonly ?CardListQuery $cards = null,
    ) {}

    public function query()
    {
        return ($this->cards ?? new CardListQuery)->query();
    }

    public function headings(): array
    {
        return [
            'Card Number',
            'Card Type',
            'Bank Name',
            'Account Number',
            'Customer Name',
            'Expiry Date',
            'Notes',
            'Created At',
        ];
    }

    public function map($card): array
    {
        return [
            $card->card_number,
            $card->card_type,
            $card->account ? $card->account->bank_name : '',
            $card->account ? $card->account->account_number : '',
            $card->account && $card->account->customer ? $card->account->customer->full_name : '',
            $card->expiry_date ? $card->expiry_date->format('Y-m-d') : '',
            $card->notes,
            $card->created_at ? $card->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
