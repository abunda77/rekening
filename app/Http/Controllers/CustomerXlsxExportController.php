<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Services\CustomerListQuery;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerXlsxExportController extends Controller
{
    /**
     * Download the customers that match the filters currently applied on the customers page.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        return Excel::download(new CustomersExport(CustomerListQuery::fromRequest($request)), 'customers.xlsx');
    }
}
