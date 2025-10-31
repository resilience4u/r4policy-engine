<?php

declare(strict_types=1);

namespace Tests\CLI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use R4Policy\CLI\ValidateCommand;
use R4Policy\Validator\PolicyValidator;
use R4Policy\Loader\YamlPolicyLoader;

final class ValidateCommandTest extends TestCase
{
    private string $validFile;
    private string $invalidFile;

    protected function setUp(): void
    {
        $this->validFile = __DIR__ . '/../../examples/resilience.yaml';
        $this->invalidFile = sys_get_temp_dir() . '/invalid.yaml';
        file_put_contents($this->invalidFile, 'invalid: [unclosed');
    }

    public function testValidateCommandSucceeds(): void
    {
        $validator = new PolicyValidator();
        $loader = new YamlPolicyLoader($validator);
        $command = new ValidateCommand($loader);

        $tester = new CommandTester($command);
        $tester->execute(['file' => $this->validFile]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('OK', $tester->getDisplay());
    }

    public function testValidateCommandFailsForInvalidYaml(): void
    {
        $validator = new PolicyValidator();
        $loader = new YamlPolicyLoader($validator);
        $command = new ValidateCommand($loader);

        $tester = new CommandTester($command);
        $tester->execute(['file' => $this->invalidFile]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('INVALID', $tester->getDisplay());
    }

    protected function tearDown(): void
    {
        @unlink($this->invalidFile);
    }
}
