<?php

declare(strict_types=1);

namespace R4Policy\Evaluator;

use R4Policy\Contracts\PolicyEvaluatorInterface;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Telemetry\TelemetryBridge;
use Resiliente\R4PHP\Core\Chain;
use Resiliente\R4PHP\Contracts\Executable;
use Resiliente\R4PHP\Policies\RetryPolicy;
use Resiliente\R4PHP\Policies\CircuitBreakerPolicy;

final class PolicyEvaluator implements PolicyEvaluatorInterface
{
    public function __construct(
        private readonly TelemetryBridge $telemetry
    ) {}

    public function evaluate(PolicyDefinition $def): callable
    {
        return function (callable $operation) use ($def) {
            $policies = [];

            if ($def->type === 'retry' || isset($def->config['retry'])) {
                $retryConfig = $def->config['retry'] ?? $def->config;
                $policies[] = RetryPolicy::fromArray($retryConfig);
            }

            if ($def->type === 'circuit_breaker' || isset($def->config['circuitBreaker'])) {
                $cbConfig = $def->config['circuitBreaker'] ?? $def->config;
                $cbConfig['name'] = $def->name;
                $policies[] = CircuitBreakerPolicy::fromArray($cbConfig);
            }

            $chain = new Chain(...$policies);

            $executable = new class($operation) implements Executable {
                public function __construct(private $fn) {}
                public function __invoke(): mixed { return ($this->fn)(); }
            };

            try {
                return $this->telemetry->measure($def, fn() => $chain->execute($executable));
            } catch (\Throwable $e) {
                $this->telemetry->recordFailure($def, $e);
                throw $e;
            }
        };
    }
}
