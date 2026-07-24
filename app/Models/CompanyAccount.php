<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAccount extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyAccountFactory> */
    use HasFactory, HasUuids;

    /** @var list<string> */
    protected $fillable = [
        'customer_id',
        'agent_id',
        'company_name',
        'bank_name',
        'branch',
        'account_number',
        'opening_date',
        'expired_on',
        'mobile_banking',
        'note',
        'cover_buku',
        'status',
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
            'expired_on' => 'date',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<Agent, $this> */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
