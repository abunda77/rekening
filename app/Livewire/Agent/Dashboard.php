<?php

namespace App\Livewire\Agent;

use App\Models\Account;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.agent')]
#[Title('Agent Dashboard')]
class Dashboard extends Component
{
    use WithPagination;

    public $selectedCustomer;

    public bool $showCustomerModal = false;

    public $selectedCard;

    public bool $showCardModal = false;

    public function render()
    {
        $agentId = Auth::guard('agent')->id();

        $accounts = Account::query()
            ->with(['customer', 'cards'])
            ->where('agent_id', $agentId)
            ->latest()
            ->paginate(10);

        return view('livewire.agent.dashboard', [
            'accounts' => $accounts,
        ]);
    }

    public $selectedAccount;

    public bool $showAccountModal = false;

    public function showCustomerDetail($customerId)
    {
        $this->selectedCustomer = \App\Models\Customer::find($customerId);
        $this->showCustomerModal = true;
    }

    public function showAccountDetail($accountId)
    {
        $this->selectedAccount = Account::with(['customer', 'cards'])->find($accountId);
        $this->showAccountModal = true;
    }

    public function showCardDetail($cardId)
    {
        $this->selectedCard = \App\Models\Card::with('account.customer')->find($cardId);
        $this->showCardModal = true;
    }

    public function printDetailPdf(string $id)
    {
        $account = Account::with(['customer', 'agent'])->findOrFail($id);
        $pdf = Pdf::loadView('exports.account-detail-pdf', ['account' => $account]);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'account_'.$account->account_number.'.pdf');
    }

    public function printCustomerPdf(string $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $pdf = Pdf::loadView('exports.customer-detail-pdf', ['customer' => $customer]);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'customer_'.$customer->nik.'.pdf');
    }

    public function closeModal()
    {
        $this->showCustomerModal = false;
        $this->showCardModal = false;
        $this->showAccountModal = false;
        $this->selectedCustomer = null;
        $this->selectedCard = null;
        $this->selectedAccount = null;
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('agent.login');
    }
}
