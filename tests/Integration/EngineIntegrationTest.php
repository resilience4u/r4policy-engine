<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use R4Policy\R4PolicyEngine;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Telemetry\TelemetryBridge;

final class EngineIntegrationTest extends TestCase
{
    private string $yamlFile;

    protected function setUp(): void
    {
        $this->yamlFile = __DIR__ . '/../fixtures/resilience.yaml';
        if (!is_dir(dirname($this->yamlFile))) {
            mkdir(dirname($this->yamlFile), 0777, true);
        }

        file_put_contents($this->yamlFile, <<<YAML
defaults:
  retry:
    maxAttempts: 2
policies:
  - name: user_api
    retry:
      maxAttempts: 2
YAML);
    }

    public function testEngineLoadsAndExecutesPolicy(): void
    {
        $engine = new R4PolicyEngine(
            new YamlPolicyLoader(new PolicyValidator()),
            new PolicyEvaluator(new TelemetryBridge())
        );

        $engine->loadFrom($this->yamlFile);

        $result = $engine->execute('user_api', fn() => "ok");

        $this->assertEquals('ok', $result);
    }
}
