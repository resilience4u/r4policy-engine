<?php

declare(strict_types=1);

namespace Tests\Evaluator;

use PHPUnit\Framework\TestCase;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Telemetry\TelemetryBridge;

final class PolicyEvaluatorRetryTest extends TestCase
{
    public function testRetrySucceedsAfterTransientFailure(): void
    {
        $def = new PolicyDefinition('user_api', 'retry', [
            'maxAttempts' => 3,
            'initialMs' => 100,
            'strategy' => 'constant',
        ]);

        $evaluator = new PolicyEvaluator(new TelemetryBridge());
        $wrapped = $evaluator->evaluate($def);

        $attempts = 0;
        $result = $wrapped(function () use (&$attempts) {
            $attempts++;
            if ($attempts < 2) {
                throw new \RuntimeException('boom');
            }
            return 'ok';
        });

        $this->assertSame('ok', $result);
        $this->assertSame(2, $attempts);
    }
}
