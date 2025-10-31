<?php

declare(strict_types=1);

namespace Tests\Loader;

use PHPUnit\Framework\TestCase;
use R4Policy\Loader\JsonPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Model\PolicyDefinition;

final class JsonPolicyLoaderTest extends TestCase
{
    public function testLoadsPoliciesFromJsonFile(): void
    {
        $file = __DIR__ . '/../fixtures/policies.json';
        file_put_contents($file, json_encode([
            'defaults' => ['retry' => ['maxAttempts' => 3]],
            'policies' => [
                ['name' => 'user_api', 'retry' => ['initialMs' => 100]],
                ['name' => 'send_email', 'retry' => ['initialMs' => 200]],
            ],
        ], JSON_PRETTY_PRINT));

        $loader = new JsonPolicyLoader(new PolicyValidator());
        $policies = $loader->load($file);

        $this->assertCount(2, $policies);
        $this->assertContainsOnlyInstancesOf(PolicyDefinition::class, $policies);

        $names = array_map(fn($p) => $p->name, $policies);
        $this->assertSame(['user_api', 'send_email'], $names);
    }

    public function testThrowsForInvalidJson(): void
    {
        $file = __DIR__ . '/../fixtures/invalid.json';
        file_put_contents($file, '{invalid_json}');

        $loader = new JsonPolicyLoader(new PolicyValidator());

        $this->expectException(\JsonException::class);
        $loader->load($file);
    }

    public function testMergesDefaultsIntoPolicies(): void
    {
        $file = __DIR__ . '/../fixtures/merge.json';
        file_put_contents($file, json_encode([
            'defaults' => ['retry' => ['maxAttempts' => 5, 'initialMs' => 50]],
            'policies' => [
                ['name' => 'custom', 'retry' => ['maxAttempts' => 10]],
            ],
        ], JSON_PRETTY_PRINT));

        $loader = new JsonPolicyLoader(new PolicyValidator());
        $policies = $loader->load($file);

        $def = $policies[0];
        $this->assertSame('custom', $def->name);
        $this->assertSame(10, $def->config['retry']['maxAttempts']);
        $this->assertSame(50, $def->config['retry']['initialMs']);
    }
}
