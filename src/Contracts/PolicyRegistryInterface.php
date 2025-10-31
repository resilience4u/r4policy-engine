<?php
namespace R4Policy\Contracts;

use R4Policy\Model\PolicyDefinition;

interface PolicyRegistryInterface
{
    public function register(PolicyDefinition $policy): void;
    public function get(string $name): ?PolicyDefinition;
    /** @return PolicyDefinition[] */
    public function all(): array;
}
