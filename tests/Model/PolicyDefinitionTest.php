<?php

declare(strict_types=1);

namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use R4Policy\Model\PolicyDefinition;

final class PolicyDefinitionTest extends TestCase
{
    public function testConvertsArrayToDefinitionAndBack(): void
    {
        $array = [
            'name' => 'user_api',
            'retry' => ['maxAttempts' => 3, 'initialMs' => 100],
            'circuitBreaker' => ['failureRatePct' => 50],
        ];

        $def = \R4Policy\Model\PolicyDefinition::fromArray($array);

        $this->assertSame('user_api', $def->name);
        $this->assertArrayHasKey('retry', $def->config);
        $this->assertSame('retry', $def->type);
        $this->assertArrayHasKey('type', $def->toArray());
    }

    public function testThrowsIfNameMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Policy name is required');

        PolicyDefinition::fromArray(['retry' => ['maxAttempts' => 3]]);
    }
}
