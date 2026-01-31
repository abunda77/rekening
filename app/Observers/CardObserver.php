<?php

namespace App\Observers;

use App\Models\AgentNotification;
use App\Models\Card;

class CardObserver
{
    /**
     * Handle the Card "created" event.
     */
    public function created(Card $card): void
    {
        $account = $card->account;
        if (! $account?->agent_id) {
            return;
        }

        $cardType = ucfirst($card->card_type ?? 'Debit');
        $maskedNumber = $this->maskCardNumber($card->card_number);
        $accountNumber = $account->account_number ?? '-';

        AgentNotification::create([
            'agent_id' => $account->agent_id,
            'type' => 'card',
            'action' => 'created',
            'notifiable_type' => Card::class,
            'notifiable_id' => $card->id,
            'title' => 'Kartu Baru Diterbitkan',
            'message' => "Kartu {$cardType} baru ({$maskedNumber}) telah diterbitkan untuk rekening {$accountNumber}.",
        ]);
    }

    /**
     * Handle the Card "updated" event.
     */
    public function updated(Card $card): void
    {
        $account = $card->account;
        if (! $account?->agent_id) {
            return;
        }

        $maskedNumber = $this->maskCardNumber($card->card_number);
        $changes = [];

        if ($card->wasChanged('card_type')) {
            $changes[] = 'tipe kartu menjadi '.ucfirst($card->card_type);
        }
        if ($card->wasChanged('expiry_date')) {
            $expiryFormatted = $card->expiry_date?->format('m/Y') ?? '-';
            $changes[] = "tanggal kadaluarsa menjadi {$expiryFormatted}";
        }

        if (empty($changes)) {
            $changeText = 'Data kartu telah diperbarui';
        } else {
            $changeText = 'Perubahan: '.implode(', ', $changes);
        }

        AgentNotification::create([
            'agent_id' => $account->agent_id,
            'type' => 'card',
            'action' => 'updated',
            'notifiable_type' => Card::class,
            'notifiable_id' => $card->id,
            'title' => 'Kartu Diperbarui',
            'message' => "Kartu {$maskedNumber} telah diperbarui. {$changeText}.",
        ]);
    }

    /**
     * Mask card number showing only last 4 digits.
     */
    private function maskCardNumber(?string $cardNumber): string
    {
        if (! $cardNumber || strlen($cardNumber) < 4) {
            return '****';
        }

        return '**** '.substr($cardNumber, -4);
    }
}
