<?php

namespace App\Http\Controllers;

use App\Exports\CompanyAccountsExport;
use App\Services\CompanyAccountListQuery;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyAccountXlsxExportController extends Controller
{
    /**
     * Download the company accounts that match the filters currently applied on the page.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        return Excel::download(new CompanyAccountsExport(CompanyAccountListQuery::fromRequest($request)), 'company-accounts.xlsx');
    }
}
