<?php

declare(strict_types=1);

namespace Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Telemetry\TelemetryBridge;

final class PolicyEvaluatorCircuitBreakerTest extends TestCase
{
    public function testEvaluatesCircuitBreakerPolicy(): void
    {
        $telemetry = new class extends TelemetryBridge {
            public bool $measured = false;
            public function measure($def, callable $fn): mixed {
                $this->measured = true;
                return $fn();
            }
        };

        $def = new PolicyDefinition('test_cb', 'circuit_breaker', [
            'circuitBreaker' => [
                'failureRatePct' => 50,
                'minSamples' => 1,
                'openMs' => 100,
                'timeWindowSec' => 1,
            ],
        ]);

        $evaluator = new PolicyEvaluator($telemetry);

        $fn = $evaluator->evaluate($def);
        $result = $fn(fn() => 'ok');

        $this->assertSame('ok', $result);
        $this->assertTrue($telemetry->measured);
    }
}
