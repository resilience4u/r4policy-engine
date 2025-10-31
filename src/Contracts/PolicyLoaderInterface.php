<?php
namespace R4Policy\Contracts;

use R4Policy\Model\PolicyDefinition;

interface PolicyLoaderInterface
{
    /** @return PolicyDefinition[] */
    public function load(string $path): array;
}
