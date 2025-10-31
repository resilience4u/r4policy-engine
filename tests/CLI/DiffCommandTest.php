<?php

declare(strict_types=1);

namespace Tests\CLI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use R4Policy\CLI\DiffCommand;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;

final class DiffCommandTest extends TestCase
{
    public function testDiffCommandShowsDifferences(): void
    {
        $f1 = __DIR__ . '/../fixtures/old.yaml';
        $f2 = __DIR__ . '/../fixtures/new.yaml';

        file_put_contents($f1, <<<YAML
policies:
  - name: user_api
    retry:
      maxAttempts: 2
YAML);

        file_put_contents($f2, <<<YAML
policies:
  - name: user_api
    retry:
      maxAttempts: 3
YAML);

        $application = new Application();
        $application->add(new DiffCommand(new YamlPolicyLoader(new PolicyValidator())));

        $command = $application->find('diff');
        $tester = new CommandTester($command);
        $tester->execute(['old' => $f1, 'new' => $f2]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('user_api', $output);
        $this->assertMatchesRegularExpression('/changed/i', $output);
    }
}
