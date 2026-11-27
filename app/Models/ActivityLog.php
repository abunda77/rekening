<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
     * Log entries are append-only, so only "created_at" is tracked.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'guard',
        'method',
        'url',
        'route_name',
        'status_code',
        'ip_address',
        'user_agent',
        'referer',
        'duration_ms',
        'payload',
        'error',
        'session_id',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status_code' => 'integer',
            'duration_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Narrow the query to a search term across the visible columns.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('url', 'like', "%{$term}%")
                ->orWhere('route_name', 'like', "%{$term}%")
                ->orWhere('ip_address', 'like', "%{$term}%")
                ->orWhere('user_name', 'like', "%{$term}%");
        });
    }

    /**
     * Narrow the query to a status class such as "2xx", "4xx", or "5xx".
     */
    public function scopeStatusClass(Builder $query, ?string $class): Builder
    {
        if (! $class || ! preg_match('/^([1-5])xx$/', $class, $matches)) {
            return $query;
        }

        $hundred = (int) $matches[1] * 100;

        return $query->whereBetween('status_code', [$hundred, $hundred + 99]);
    }

    /**
     * Flux badge color matching the HTTP status.
     */
    public function statusColor(): string
    {
        return match (true) {
            $this->status_code >= 500 => 'red',
            $this->status_code >= 400 => 'amber',
            $this->status_code >= 300 => 'blue',
            $this->status_code >= 200 => 'green',
            default => 'zinc',
        };
    }

    /**
     * Flux badge color matching the HTTP method.
     */
    public function methodColor(): string
    {
        return match (strtoupper($this->method)) {
            'GET' => 'zinc',
            'POST' => 'green',
            'PUT', 'PATCH' => 'amber',
            'DELETE' => 'red',
            default => 'blue',
        };
    }
}
