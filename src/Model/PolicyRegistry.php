<?php
namespace R4Policy\Model;

use R4Policy\Contracts\PolicyRegistryInterface;

final class PolicyRegistry implements PolicyRegistryInterface
{
    /** @var array<string,PolicyDefinition> */
    private array $map = [];

    public function register(PolicyDefinition $policy): void
    {
        $this->map[$policy->name] = $policy;
    }

    public function get(string $name): ?PolicyDefinition
    {
        return $this->map[$name] ?? null;
    }

    /** @return PolicyDefinition[] */
    public function all(): array
    {
        return array_values($this->map);
    }
}
