<?php

namespace App\Models;

use Database\Factories\AgentSupplierFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentSupplier extends Model
{
    /** @use HasFactory<AgentSupplierFactory> */
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'contact_number',
    ];

    /**
     * Get bank accounts supplied by this agent supplier.
     *
     * @return HasMany<Account, $this>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get company accounts supplied by this agent supplier.
     *
     * @return HasMany<CompanyAccount, $this>
     */
    public function companyAccounts(): HasMany
    {
        return $this->hasMany(CompanyAccount::class);
    }
}
