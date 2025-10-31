<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use R4Policy\R4PolicyEngine;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Telemetry\TelemetryBridge;

final class EngineReloadTest extends TestCase
{
    public function testReloadKeepsPolicies(): void
    {
        $file = __DIR__ . '/../fixtures/reload.yaml';
        file_put_contents($file, <<<YAML
policies:
  - name: test_policy
    retry:
      maxAttempts: 1
YAML);

        $engine = new R4PolicyEngine(
            new YamlPolicyLoader(new PolicyValidator()),
            new PolicyEvaluator(new TelemetryBridge())
        );
        $engine->loadFrom($file);

        $first = $engine->get('test_policy');
        $engine->reload();
        $second = $engine->get('test_policy');

        $this->assertEquals($first->name, $second->name);
    }
}
