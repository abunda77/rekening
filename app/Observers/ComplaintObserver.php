<?php

namespace App\Observers;

use App\Models\AgentNotification;
use App\Models\Complaint;

class ComplaintObserver
{
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        if (! $complaint->agent_id) {
            return;
        }

        $customerName = $complaint->customer?->full_name ?? 'Unknown';
        $subject = $complaint->subject ?? 'Tanpa Subjek';

        AgentNotification::create([
            'agent_id' => $complaint->agent_id,
            'type' => 'complaint',
            'action' => 'created',
            'notifiable_type' => Complaint::class,
            'notifiable_id' => $complaint->id,
            'title' => 'Komplain Baru Diterima',
            'message' => "Komplain baru: \"{$subject}\" dari nasabah {$customerName}.",
        ]);
    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint): void
    {
        if (! $complaint->agent_id) {
            return;
        }

        $subject = $complaint->subject ?? 'Tanpa Subjek';
        $changes = [];

        if ($complaint->wasChanged('status')) {
            $statusLabel = $this->getStatusLabel($complaint->status);
            $changes[] = "status menjadi {$statusLabel}";
        }
        if ($complaint->wasChanged('agent_id')) {
            $changes[] = 'ditugaskan ke agent baru';
        }

        if (empty($changes)) {
            $changeText = 'Data komplain telah diperbarui';
        } else {
            $changeText = 'Perubahan: '.implode(', ', $changes);
        }

        AgentNotification::create([
            'agent_id' => $complaint->agent_id,
            'type' => 'complaint',
            'action' => 'updated',
            'notifiable_type' => Complaint::class,
            'notifiable_id' => $complaint->id,
            'title' => 'Komplain Diperbarui',
            'message' => "Komplain \"{$subject}\" telah diperbarui. {$changeText}.",
        ]);
    }

    /**
     * Get human-readable status label.
     */
    private function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'open' => 'Dibuka',
            'in_progress' => 'Dalam Proses',
            'resolved' => 'Terselesaikan',
            'closed' => 'Ditutup',
            default => $status ?? 'Unknown',
        };
    }
}
