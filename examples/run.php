<?php

use R4Policy\R4PolicyEngine;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Evaluator\PolicyEvaluator;
use R4Policy\Telemetry\TelemetryBridge;

require __DIR__ . '/../vendor/autoload.php';

$engine = new R4PolicyEngine(
    new YamlPolicyLoader(new PolicyValidator()),
    new PolicyEvaluator(new TelemetryBridge())
);

$engine->loadFrom(__DIR__ . '/resilience.yaml');

$policy = $engine->get('user_api');

$result = $policy(function () {
    static $i = 0;
    $i++;
    echo "Attempt {$i}...\n";
    if ($i < 2) {
        throw new RuntimeException('💥 simulated failure');
    }
    return "✅ success on attempt {$i}";
});

echo $result . PHP_EOL;
