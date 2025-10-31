<?php

declare(strict_types=1);

namespace Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Telemetry\TelemetryBridge;

final class PolicyEvaluatorErrorTest extends TestCase
{
    public function testEvaluatorRecordsFailureAndThrows(): void
    {
        $def = new PolicyDefinition('user_api', 'retry', ['maxAttempts' => 1]);
        $evaluator = new PolicyEvaluator(new TelemetryBridge());

        $this->expectException(\RuntimeException::class);
        $evaluator->evaluate($def)(function () {
            throw new \RuntimeException('boom');
        });
    }
}
