<?php

namespace App\Observers;

use App\Models\AgentNotification;
use App\Models\Shipment;

class ShipmentObserver
{
    /**
     * Handle the Shipment "created" event.
     */
    public function created(Shipment $shipment): void
    {
        if (! $shipment->agent_id) {
            return;
        }

        $expedition = $shipment->expedition ?? 'Unknown';
        $accountNumber = $shipment->account?->account_number ?? '-';

        AgentNotification::create([
            'agent_id' => $shipment->agent_id,
            'type' => 'shipment',
            'action' => 'created',
            'notifiable_type' => Shipment::class,
            'notifiable_id' => $shipment->id,
            'title' => 'Pengiriman Baru Dibuat',
            'message' => "Pengiriman baru dibuat untuk rekening {$accountNumber} via {$expedition}.",
        ]);
    }

    /**
     * Handle the Shipment "updated" event.
     */
    public function updated(Shipment $shipment): void
    {
        if (! $shipment->agent_id) {
            return;
        }

        $receiptNumber = $shipment->receipt_number ?? '-';
        $changes = [];

        if ($shipment->wasChanged('status')) {
            $statusLabel = $this->getStatusLabel($shipment->status);
            $changes[] = "status menjadi {$statusLabel}";
        }
        if ($shipment->wasChanged('receipt_number') && $shipment->receipt_number) {
            $changes[] = "resi: {$shipment->receipt_number}";
        }
        if ($shipment->wasChanged('expedition')) {
            $changes[] = "ekspedisi menjadi {$shipment->expedition}";
        }

        if (empty($changes)) {
            $changeText = 'Data pengiriman telah diperbarui';
        } else {
            $changeText = implode(', ', $changes);
        }

        AgentNotification::create([
            'agent_id' => $shipment->agent_id,
            'type' => 'shipment',
            'action' => 'updated',
            'notifiable_type' => Shipment::class,
            'notifiable_id' => $shipment->id,
            'title' => 'Status Pengiriman Diperbarui',
            'message' => "Pengiriman (Resi: {$receiptNumber}) - {$changeText}.",
        ]);
    }

    /**
     * Get human-readable status label.
     */
    private function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            default => $status ?? 'Unknown',
        };
    }
}
