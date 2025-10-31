<?php

declare(strict_types=1);

namespace R4Policy\Contracts;

use R4Policy\Model\PolicyDefinition;

interface PolicyEvaluatorInterface
{
    /**
     * Retorna um callable que executa uma operação com a política aplicada.
     *
     * @param PolicyDefinition $def
     * @return callable(callable): mixed
     */
    public function evaluate(PolicyDefinition $def): callable;
}
