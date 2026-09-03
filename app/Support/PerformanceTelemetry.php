<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class PerformanceTelemetry
{
    public static function enabled(): bool
    {
        return (bool) config('observability.enabled', false);
    }

    public static function start(): int
    {
        return hrtime(true);
    }

    /**
     * Record only operation names and timing metadata. Request values, bindings,
     * document names, and credentials must never be passed as context.
     *
     * @param array<string, bool|float|int|string> $context
     */
    public static function record(string $operation, int $startedAt, array $context = []): void
    {
        if (! self::enabled()) {
            return;
        }

        Log::channel(config('observability.channel', 'stack'))->info('performance.measurement', [
            'operation' => $operation,
            'duration_ms' => round((hrtime(true) - $startedAt) / 1_000_000, 2),
            ...$context,
        ]);
    }
}
