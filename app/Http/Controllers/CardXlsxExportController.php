<?php

namespace App\Http\Controllers;

use App\Exports\CardsExport;
use App\Services\CardListQuery;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CardXlsxExportController extends Controller
{
    /**
     * Download the cards that match the filters currently applied on the cards page.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        return Excel::download(new CardsExport(CardListQuery::fromRequest($request)), 'cards.xlsx');
    }
}
