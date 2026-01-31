<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AgentNotification extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'agent_id',
        'type',
        'action',
        'notifiable_type',
        'notifiable_id',
        'title',
        'message',
        'read_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the agent that owns this notification.
     *
     * @return BelongsTo<Agent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the notifiable model (Complaint, Shipment, Account, or Card).
     *
     * @return MorphTo<Model, $this>
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include unread notifications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AgentNotification>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AgentNotification>
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<AgentNotification>  $query
     * @return \Illuminate\Database\Eloquent\Builder<AgentNotification>
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Determine if the notification has been read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Get the icon name based on notification type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'account' => 'credit-card',
            'card' => 'credit-card',
            'shipment' => 'truck',
            'complaint' => 'exclamation-circle',
            default => 'bell',
        };
    }

    /**
     * Get the color class based on notification type.
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'account' => 'blue',
            'card' => 'purple',
            'shipment' => 'indigo',
            'complaint' => 'rose',
            default => 'slate',
        };
    }
}
