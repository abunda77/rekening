<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Card;
use App\Models\CompanyAccount;
use App\Models\Complaint;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $nextMonthStart = now()->startOfMonth()->addMonth();
        $nextMonthEnd = $nextMonthStart->copy()->endOfMonth();

        $totalAgents = Agent::count();
        $totalCustomers = Customer::count();
        $totalAccounts = Account::count();
        $totalCompanyAccounts = CompanyAccount::count();
        $companyAccountsExpiringThisMonth = CompanyAccount::query()
            ->whereBetween('expired_on', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $totalAtms = Card::count();

        // Rekening Bank
        $expiringAccounts = Account::with(['customer', 'agent'])
            ->whereBetween('expired_on', [$currentMonthStart, $currentMonthEnd])
            ->latest('expired_on')
            ->paginate(10, ['*'], 'bank-this-month');

        $nextMonthAccounts = Account::with(['customer', 'agent'])
            ->whereBetween('expired_on', [$nextMonthStart, $nextMonthEnd])
            ->latest('expired_on')
            ->paginate(10, ['*'], 'bank-next-month');

        // Rekening PT
        $expiringCompanyAccounts = CompanyAccount::with(['customer', 'agent'])
            ->whereBetween('expired_on', [$currentMonthStart, $currentMonthEnd])
            ->latest('expired_on')
            ->paginate(10, ['*'], 'pt-this-month');

        $nextMonthCompanyAccounts = CompanyAccount::with(['customer', 'agent'])
            ->whereBetween('expired_on', [$nextMonthStart, $nextMonthEnd])
            ->latest('expired_on')
            ->paginate(10, ['*'], 'pt-next-month');

        $pendingComplaints = Complaint::with(['customer', 'agent', 'account'])
            ->pending()
            ->latest()
            ->paginate(10, ['*'], 'complaints');

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
            'pendingComplaints'
        ));
    }
}
