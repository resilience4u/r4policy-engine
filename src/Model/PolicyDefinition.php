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

        // detecta tipo
        $type = $data['type'] ?? (
        isset($data['retry']) ? 'retry'
            : (isset($data['circuitBreaker']) ? 'circuit_breaker' : 'unknown')
        );

        // remove metadados da config
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

        // adiciona type apenas se existir
        if ($this->type !== 'unknown') {
            $arr['type'] = $this->type;
        }

        return $arr;
    }
}
