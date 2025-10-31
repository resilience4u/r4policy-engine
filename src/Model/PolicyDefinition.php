<?php

declare(strict_types=1);

namespace R4Policy\Model;

final class PolicyDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public array $config = [],
        public array $raw = []
    ) {}

    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Policy name is required');
        }

        $type = $data['type'] ?? (
        isset($data['retry']) ? 'retry'
            : (isset($data['circuitBreaker']) ? 'circuit_breaker' : 'unknown')
        );

        $config = $data;
        unset($config['name'], $config['type']);

        return new self(
            $data['name'],
            $type,
            $config,
            $data
        );
    }

    public function toArray(): array
    {
        $arr = [
                'name' => $this->name,
            ] + $this->config;

        if ($this->type !== 'unknown') {
            $arr['type'] = $this->type;
        }

        return $arr;
    }
}
