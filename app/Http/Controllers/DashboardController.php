<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Card;
use App\Models\CompanyAccount;
use App\Models\Complaint;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Expired account tables rendered on the dashboard, keyed by export type.
     *
     * @var array<string, array{model: class-string, period: string, title: string, company: bool, filename: string}>
     */
    private const EXPIRED_TABLES = [
        'bank-this-month' => [
            'model' => Account::class,
            'period' => 'this-month',
            'title' => 'Rekening Bank — Expired Bulan Ini',
            'company' => false,
            'filename' => 'rekening-bank-expired-bulan-ini.pdf',
        ],
        'bank-next-month' => [
            'model' => Account::class,
            'period' => 'next-month',
            'title' => 'Rekening Bank — Expired Bulan Depan',
            'company' => false,
            'filename' => 'rekening-bank-expired-bulan-depan.pdf',
        ],
        'pt-this-month' => [
            'model' => CompanyAccount::class,
            'period' => 'this-month',
            'title' => 'Rekening PT — Expired Bulan Ini',
            'company' => true,
            'filename' => 'rekening-pt-expired-bulan-ini.pdf',
        ],
        'pt-next-month' => [
            'model' => CompanyAccount::class,
            'period' => 'next-month',
            'title' => 'Rekening PT — Expired Bulan Depan',
            'company' => true,
            'filename' => 'rekening-pt-expired-bulan-depan.pdf',
        ],
    ];

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $totalAgents = Agent::count();
        $totalCustomers = Customer::count();
        $totalAccounts = Account::count();
        $totalCompanyAccounts = CompanyAccount::count();
        $totalAtms = Card::count();

        $expiringAccounts = $this->expiredQuery('bank-this-month')
            ->paginate(10, ['*'], 'bank-this-month');

        $nextMonthAccounts = $this->expiredQuery('bank-next-month')
            ->paginate(10, ['*'], 'bank-next-month');

        $expiringCompanyAccounts = $this->expiredQuery('pt-this-month')
            ->paginate(10, ['*'], 'pt-this-month');

        $nextMonthCompanyAccounts = $this->expiredQuery('pt-next-month')
            ->paginate(10, ['*'], 'pt-next-month');

        $companyAccountsExpiringThisMonth = $expiringCompanyAccounts->total();

        $pendingComplaints = Complaint::with(['customer', 'agent', 'account'])
            ->pending()
            ->latest()
            ->paginate(10, ['*'], 'complaints');

        $titles = array_map(fn (array $table) => $table['title'], self::EXPIRED_TABLES);

        return view('dashboard', compact(
            'totalAgents',
            'totalCustomers',
            'totalAccounts',
            'totalCompanyAccounts',
            'companyAccountsExpiringThisMonth',
            'totalAtms',
            'expiringAccounts',
            'nextMonthAccounts',
            'expiringCompanyAccounts',
            'nextMonthCompanyAccounts',
            'pendingComplaints',
            'titles'
        ));
    }

    /**
     * Download the expired accounts of a dashboard table as a PDF.
     */
    public function exportPdf(string $type): StreamedResponse
    {
        abort_unless(isset(self::EXPIRED_TABLES[$type]), 404);

        $table = self::EXPIRED_TABLES[$type];
        $accounts = $this->expiredQuery($type)->get();

        $pdf = Pdf::loadView('exports.dashboard-expired-pdf', [
            'title' => $table['title'],
            'accounts' => $accounts,
            'showCompany' => $table['company'],
        ]);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $table['filename'], ['Content-Type' => 'application/pdf']);
    }

    /**
     * Build the expired accounts query for the given dashboard table.
     */
    private function expiredQuery(string $type): Builder
    {
        $table = self::EXPIRED_TABLES[$type];
        [$start, $end] = $this->periodRange($table['period']);

        return $table['model']::query()
            ->with(['customer', 'agent'])
            ->whereBetween('expired_on', [$start, $end])
            ->latest('expired_on');
    }

    /**
     * Resolve the start and end date of the requested period.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periodRange(string $period): array
    {
        $start = $period === 'next-month'
            ? now()->startOfMonth()->addMonth()
            : now()->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }
}
