<?php

declare(strict_types=1);

namespace Tests\Validator;

use PHPUnit\Framework\TestCase;
use R4Policy\Validator\PolicyValidator;
use RuntimeException;

final class PolicyValidatorTest extends TestCase
{
    public function testValidPolicyPasses(): void
    {
        $validator = new PolicyValidator();
        $doc = [
            'policies' => [
                ['name' => 'user_api', 'retry' => ['maxAttempts' => 3]],
            ],
        ];

        $this->expectNotToPerformAssertions();
        $validator->validate($doc, 'inline');
    }

    public function testThrowsForMissingName(): void
    {
        $this->expectException(RuntimeException::class);
        $validator = new PolicyValidator();
        $validator->validate(['policies' => [['retry' => ['maxAttempts' => 3]]]], 'inline');
    }

    public function testThrowsForInvalidType(): void
    {
        $this->expectException(RuntimeException::class);
        $validator = new PolicyValidator();
        $validator->validate(['policies' => 'invalid'], 'inline');
    }
}
