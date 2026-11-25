<?php

namespace App\Http\Controllers;

use App\Exports\AccountsExport;
use App\Services\AccountListQuery;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccountXlsxExportController extends Controller
{
    /**
     * Download the accounts that match the filters currently applied on the accounts page.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        return Excel::download(new AccountsExport(AccountListQuery::fromRequest($request)), 'accounts.xlsx');
    }
}
