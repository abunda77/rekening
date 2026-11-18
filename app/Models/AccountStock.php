<?php

namespace App\Models;

use Database\Factories\AccountStockFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountStock extends Model
{
    /** @use HasFactory<AccountStockFactory> */
    use HasFactory, HasUuids;

    /**
     * Owner type of the stock account: personal or PT.
     */
    public const TYPE_PERSONAL = 'personal';

    public const TYPE_PT = 'pt';

    /**
     * Available owner types keyed by stored value.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        self::TYPE_PERSONAL => 'Personal',
        self::TYPE_PT => 'PT',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_number',
        'customer_name',
        'bank_name',
        'account_type',
        'agent_id',
        'is_in_stock',
        'user',
        'note',
        'is_vip',
        'image',
    ];

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'account_type' => self::TYPE_PERSONAL,
        'is_in_stock' => true,
        'is_vip' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_in_stock' => 'boolean',
            'is_vip' => 'boolean',
        ];
    }

    /**
     * Human readable label of the owner type.
     */
    public function typeLabel(): string
    {
        return self::TYPES[$this->account_type] ?? '-';
    }

    /**
     * Get the agent that owns this stock.
     *
     * @return BelongsTo<Agent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
