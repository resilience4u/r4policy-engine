<?php

declare(strict_types=1);

namespace Tests\Registry;

use PHPUnit\Framework\TestCase;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Model\PolicyRegistry;

final class PolicyRegistryTest extends TestCase
{
    public function testRegistersAndRetrievesPolicies(): void
    {
        $registry = new PolicyRegistry();
        $policy = new PolicyDefinition('user_api', 'retry', ['maxAttempts' => 3]);

        $registry->register($policy);
        $retrieved = $registry->get('user_api');

        $this->assertNotNull($retrieved);
        $this->assertSame($policy->name, $retrieved->name);
        $this->assertSame($policy->type, $retrieved->type);
    }

    public function testGetReturnsNullForUnknownPolicy(): void
    {
        $registry = new PolicyRegistry();
        $this->assertNull($registry->get('nonexistent'));
    }
}
