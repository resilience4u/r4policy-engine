<?php

declare(strict_types=1);

namespace Tests\CLI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use R4Policy\CLI\DumpCommand;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;

final class DumpCommandTest extends TestCase
{
    public function testDumpCommandDisplaysPolicies(): void
    {
        $yaml = __DIR__ . '/../fixtures/resilience.yaml';
        file_put_contents($yaml, <<<YAML
policies:
  - name: user_api
    retry:
      maxAttempts: 2
  - name: send_email
    retry:
      maxAttempts: 1
YAML);

        $application = new Application();
        $application->add(new DumpCommand(new YamlPolicyLoader(new PolicyValidator())));

        $command = $application->find('dump');
        $tester = new CommandTester($command);
        $tester->execute(['file' => $yaml]);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('user_api', $output);
        $this->assertStringContainsString('send_email', $output);
    }
}
