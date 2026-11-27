<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LogActivity
{
    /**
     * Guards checked to resolve the acting user, in priority order.
     */
    private const GUARDS = ['web', 'agent', 'sanctum'];

    /**
     * Handle an incoming request and record it once the response is known.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->record(
                $request,
                $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500,
                $this->durationMs($startedAt),
                $exception,
            );

            throw $exception;
        }

        $this->record($request, $response->getStatusCode(), $this->durationMs($startedAt));

        return $response;
    }

    /**
     * Persist the request. Logging must never break the request itself.
     */
    protected function record(Request $request, int $statusCode, int $durationMs, ?Throwable $exception = null): void
    {
        if (! $this->shouldLog($request)) {
            return;
        }

        try {
            [$user, $guard] = $this->resolveUser($request);

            ActivityLog::create([
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name ?? $user?->agent_name ?? $user?->email,
                'guard' => $guard,
                'method' => strtoupper($request->method()),
                'url' => Str::limit($request->fullUrl(), 2000, ''),
                'route_name' => $request->route()?->getName(),
                'status_code' => $statusCode,
                'ip_address' => (string) ($request->ip() ?? 'unknown'),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
                'referer' => Str::limit((string) $request->headers->get('referer'), 1000, ''),
                'duration_ms' => $durationMs,
                'payload' => $this->sanitizedPayload($request),
                'error' => $exception ? Str::limit($exception::class.': '.$exception->getMessage(), 250, '') : null,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'created_at' => now(),
            ]);
        } catch (Throwable $error) {
            report($error);
        }
    }

    /**
     * Determine whether the current request is worth recording.
     */
    protected function shouldLog(Request $request): bool
    {
        if (! config('activity-log.enabled')) {
            return false;
        }

        if (in_array($request->method(), config('activity-log.ignore_methods', []), true)) {
            return false;
        }

        foreach (config('activity-log.ignore_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Resolve the authenticated user and the guard that authenticated them.
     *
     * @return array{0: object|null, 1: string|null}
     */
    protected function resolveUser(Request $request): array
    {
        foreach (self::GUARDS as $guard) {
            $user = $request->user($guard);

            if ($user) {
                return [$user, $guard];
            }
        }

        return [null, null];
    }

    /**
     * Input data with sensitive values masked and oversized payloads trimmed.
     *
     * @return array<string, mixed>|null
     */
    protected function sanitizedPayload(Request $request): ?array
    {
        $payload = $this->redact($request->all(), 0);

        if ($payload === []) {
            return null;
        }

        $encoded = json_encode($payload);

        if ($encoded === false || strlen($encoded) > config('activity-log.payload_max_length', 16000)) {
            return ['_truncated' => true, 'keys' => array_keys($payload)];
        }

        return $payload;
    }

    /**
     * Recursively mask sensitive input values.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    protected function redact(array $data, int $depth): array
    {
        $redacted = [];

        foreach ($data as $key => $value) {
            if (is_string($key) && $this->isSensitiveKey($key)) {
                $redacted[$key] = '[redacted]';

                continue;
            }

            $redacted[$key] = match (true) {
                $value instanceof UploadedFile => '[file] '.$value->getClientOriginalName(),
                is_array($value) => $depth >= 2 ? '[array]' : $this->redact($value, $depth + 1),
                is_object($value) => '[object]',
                is_string($value) => Str::limit($value, 255),
                default => $value,
            };
        }

        return $redacted;
    }

    /**
     * Whether the input key holds a secret that must not be stored.
     */
    protected function isSensitiveKey(string $key): bool
    {
        return Str::contains(Str::lower($key), config('activity-log.redacted_key_fragments', []));
    }

    /**
     * Elapsed milliseconds since the request started.
     */
    protected function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
