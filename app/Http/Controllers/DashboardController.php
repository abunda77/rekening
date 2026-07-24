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
        $totalAgents = Agent::count();
        $totalCustomers = Customer::count();
        $totalAccounts = Account::count();
        $totalCompanyAccounts = CompanyAccount::count();
        $companyAccountsExpiringThisMonth = CompanyAccount::query()
            ->whereBetween('expired_on', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $totalAtms = Card::count();

        $expiringAccounts = Account::with(['customer', 'agent'])
            ->whereMonth('expired_on', now()->month)
            ->whereYear('expired_on', now()->year)
            ->latest('expired_on')
            ->paginate(10);

        $expiringCompanyAccounts = CompanyAccount::with(['customer', 'agent'])
            ->whereBetween('expired_on', [now()->startOfMonth(), now()->endOfMonth()])
            ->latest('expired_on')
            ->paginate(10, ['*'], 'company-accounts');

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
            'expiringCompanyAccounts',
            'pendingComplaints'
        ));
    }
}
