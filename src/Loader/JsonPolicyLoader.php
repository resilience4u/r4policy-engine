<?php
namespace R4Policy\Loader;

use R4Policy\Contracts\PolicyLoaderInterface;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Validator\PolicyValidator;

final class JsonPolicyLoader implements PolicyLoaderInterface
{
    public function __construct(private PolicyValidator $validator) {}

    public function load(string $path): array
    {
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $this->validator->validate($data, $path);

        $defs = [];
        $defaults = $data['defaults'] ?? [];
        foreach ($data['policies'] ?? [] as $raw) {
            $merged = array_replace_recursive($defaults, $raw);
            $defs[] = PolicyDefinition::fromArray($merged);
        }
        return $defs;
    }
}
