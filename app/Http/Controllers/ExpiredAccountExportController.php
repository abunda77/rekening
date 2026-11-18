<?php

namespace App\Http\Controllers;

use App\Services\ExpiredAccountQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpiredAccountExportController extends Controller
{
    /**
     * Download the "Rekening Expired" rows that match the active filters as a PDF.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $type = (string) $request->query('type', '');
        $period = (string) $request->query('period', 'this-month');
        $status = (string) $request->query('status', '');
        $search = trim((string) $request->query('search', ''));

        abort_unless(
            ExpiredAccountQuery::isValidType($type)
            && ExpiredAccountQuery::isValidPeriod($period)
            && ExpiredAccountQuery::isValidStatus($status),
            404
        );

        $query = new ExpiredAccountQuery($type, $period, $status, $search);

        $rows = $query->get(
            (string) $request->query('sort', ExpiredAccountQuery::DEFAULT_SORT),
            (string) $request->query('direction', 'asc'),
        );

        $pdf = Pdf::loadView('exports.expired-accounts-pdf', [
            'title' => $query->title(),
            'rows' => $rows,
            'status' => $status,
            'search' => $search,
            'showType' => $type === '',
            'showCompany' => $type !== 'bank',
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $query->filename(), ['Content-Type' => 'application/pdf']);
    }
}
