<?php

namespace App\Livewire\Agent;

use App\Models\Account;
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

    public function showCustomerDetail($customerId)
    {
        $this->selectedCustomer = \App\Models\Customer::find($customerId);
        $this->showCustomerModal = true;
    }

    public function showCardDetail($cardId)
    {
        $this->selectedCard = \App\Models\Card::with('account.customer')->find($cardId);
        $this->showCardModal = true;
    }

    public function closeModal()
    {
        $this->showCustomerModal = false;
        $this->showCardModal = false;
        $this->selectedCustomer = null;
        $this->selectedCard = null;
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('agent.login');
    }
}
