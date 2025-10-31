<?php

declare(strict_types=1);

namespace Tests\Loader;

use PHPUnit\Framework\TestCase;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Model\PolicyDefinition;

final class YamlPolicyLoaderTest extends TestCase
{
    public function testLoadsAndMergesDefaults(): void
    {
        $yaml = <<<YAML
defaults:
  retry:
    maxAttempts: 2
policies:
  - name: user_api
    retry:
      delay: 100ms
YAML;

        $file = __DIR__ . '/../fixtures/test-policy.yaml';
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, $yaml);

        $loader = new YamlPolicyLoader(new PolicyValidator());
        $policies = $loader->load($file);

        $this->assertIsArray($policies);
        $this->assertCount(1, $policies);
        $this->assertInstanceOf(PolicyDefinition::class, $policies[0]);
        $this->assertEquals('user_api', $policies[0]->name);
        $this->assertArrayHasKey('retry', $policies[0]->config);
        $this->assertEquals(2, $policies[0]->config['retry']['maxAttempts']);
    }
}
