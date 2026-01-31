<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\AgentNotification;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $account): void
    {
        if (! $account->agent_id) {
            return;
        }

        $customerName = $account->customer?->full_name ?? 'Unknown';
        $bankName = $account->bank_name ?? 'Unknown Bank';
        $accountNumber = $account->account_number ?? '-';

        AgentNotification::create([
            'agent_id' => $account->agent_id,
            'type' => 'account',
            'action' => 'created',
            'notifiable_type' => Account::class,
            'notifiable_id' => $account->id,
            'title' => 'Rekening Baru Ditambahkan',
            'message' => "Rekening baru {$bankName} ({$accountNumber}) telah ditambahkan untuk nasabah {$customerName}.",
        ]);
    }

    /**
     * Handle the Account "updated" event.
     */
    public function updated(Account $account): void
    {
        if (! $account->agent_id) {
            return;
        }

        $accountNumber = $account->account_number ?? '-';
        $changes = [];

        if ($account->wasChanged('status')) {
            $changes[] = "status menjadi {$account->status}";
        }
        if ($account->wasChanged('bank_name')) {
            $changes[] = "nama bank menjadi {$account->bank_name}";
        }
        if ($account->wasChanged('mobile_banking')) {
            $mbStatus = $account->mobile_banking ? 'aktif' : 'nonaktif';
            $changes[] = "mobile banking {$mbStatus}";
        }

        if (empty($changes)) {
            $changeText = 'Data rekening telah diperbarui';
        } else {
            $changeText = 'Perubahan: '.implode(', ', $changes);
        }

        AgentNotification::create([
            'agent_id' => $account->agent_id,
            'type' => 'account',
            'action' => 'updated',
            'notifiable_type' => Account::class,
            'notifiable_id' => $account->id,
            'title' => 'Rekening Diperbarui',
            'message' => "Rekening {$accountNumber} telah diperbarui. {$changeText}.",
        ]);
    }
}
