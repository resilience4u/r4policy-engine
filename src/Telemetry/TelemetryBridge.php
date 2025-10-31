<?php

declare(strict_types=1);

namespace R4Policy\Telemetry;

use R4Policy\Model\PolicyDefinition;

class TelemetryBridge
{
    public function __construct(private ?object $exporter = null) {}

    public function recordExecution(string $policy, string $outcome, float $elapsedSeconds): void
    {
    }

    public function recordSuccess(PolicyDefinition $def): void
    {
        $this->recordExecution($def->name, 'success', 0.0);
    }

    public function recordFailure(PolicyDefinition $def, \Throwable $e): void
    {
        $this->recordExecution($def->name, 'failure', 0.0);
    }

    /**
     * Mede o tempo e executa uma callback, registrando telemetria básica.
     */
    public function measure(PolicyDefinition $def, callable $fn): mixed
    {
        $start = microtime(true);
        try {
            $result = $fn();
            $elapsed = microtime(true) - $start;
            $this->recordExecution($def->name, 'success', $elapsed);
            return $result;
        } catch (\Throwable $e) {
            $elapsed = microtime(true) - $start;
            $this->recordExecution($def->name, 'failure', $elapsed);
            throw $e;
        }
    }
}
