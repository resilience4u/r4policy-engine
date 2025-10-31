<?php
namespace R4Policy\Loader;

use R4Policy\Contracts\PolicyLoaderInterface;
use R4Policy\Model\PolicyDefinition;
use R4Policy\Validator\PolicyValidator;
use Symfony\Component\Yaml\Yaml;

final class YamlPolicyLoader implements PolicyLoaderInterface
{
    public function __construct(public PolicyValidator $validator) {}

    public function load(string $path): array
    {
        $data = Yaml::parseFile($path) ?? [];
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
