<?php

declare(strict_types=1);

namespace R4Policy;

use R4Policy\Contracts\PolicyEvaluatorInterface;
use R4Policy\Contracts\PolicyLoaderInterface;
use R4Policy\Model\PolicyRegistry;

final class R4PolicyEngine
{
    private PolicyRegistry $registry;
    private string $lastLoadedFile = '';

    public function __construct(
        private readonly PolicyLoaderInterface $loader,
        private readonly PolicyEvaluatorInterface $evaluator
    ) {
        $this->registry = new PolicyRegistry();
    }

    public function execute(string $name, callable $operation): mixed
    {
        $policy = $this->get($name);
        if (!$policy) {
            throw new \RuntimeException("Policy '{$name}' not found.");
        }

        $evaluator = $this->evaluator->evaluate($policy);
        return $evaluator($operation);
    }


    public function loadFrom(string $file): void
    {
        $policies = $this->loader->load($file);
        $this->registry = new PolicyRegistry();

        foreach ($policies as $p) {
            $this->registry->register($p);
        }

        $this->lastLoadedFile = $file;
    }

    public function reload(): void
    {
        if ($this->lastLoadedFile && file_exists($this->lastLoadedFile)) {
            $this->loadFrom($this->lastLoadedFile);
        }
    }

    public function get(string $name): ?object
    {
        return $this->registry->get($name);
    }

    public function all(): array
    {
        return $this->registry->all();
    }
}
