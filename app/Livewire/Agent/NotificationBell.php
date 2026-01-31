<?php

namespace App\Livewire\Agent;

use App\Models\AgentNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    /**
     * Get the polling interval from config.
     */
    public function getPollingInterval(): int
    {
        return config('notifications.polling_interval', 5);
    }

    /**
     * Refresh notification count.
     */
    public function refreshNotifications(): void
    {
        $agentId = Auth::guard('agent')->id();

        if ($agentId) {
            $this->unreadCount = AgentNotification::where('agent_id', $agentId)
                ->unread()
                ->count();
        }
    }

    /**
     * Get recent notifications for dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, AgentNotification>
     */
    public function getNotificationsProperty()
    {
        $agentId = Auth::guard('agent')->id();

        if (! $agentId) {
            return collect();
        }

        $limit = config('notifications.dropdown_limit', 10);

        return AgentNotification::where('agent_id', $agentId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $id): void
    {
        $agentId = Auth::guard('agent')->id();

        $notification = AgentNotification::where('agent_id', $agentId)
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        $agentId = Auth::guard('agent')->id();

        AgentNotification::where('agent_id', $agentId)
            ->unread()
            ->update(['read_at' => now()]);

        $this->refreshNotifications();
    }

    public function mount(): void
    {
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.agent.notification-bell');
    }
}
