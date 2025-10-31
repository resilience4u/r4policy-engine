<?php
namespace R4Policy\Validator;

use JsonSchema\Validator as JsonValidator;
use JsonSchema\SchemaStorage;
use JsonSchema\Constraints\Factory;
use RuntimeException;

final class PolicyValidator
{
    private array $schema;

    public function __construct(?string $schemaPath = null)
    {
        $schemaPath ??= __DIR__ . '/policy.schema.json';
        $this->schema = json_decode(file_get_contents($schemaPath), true, 512, JSON_THROW_ON_ERROR);
    }

    public function validate(array $doc, string $source = '<memory>'): void
    {
        $schemaStorage = new SchemaStorage();
        $validator = new JsonValidator(new Factory($schemaStorage));

        $objectified = $this->toObject($doc);
        $schema = $this->toObject($this->schema);

        $validator->validate($objectified, $schema);

        if (!$validator->isValid()) {
            $errors = array_map(
                fn($e) => sprintf("%s: %s", $e['property'] ?: '$', $e['message']),
                $validator->getErrors()
            );
            throw new RuntimeException("Invalid policy file ($source):\n- " . implode("\n- ", $errors));
        }
    }

    private function toObject(mixed $value): mixed
    {
        if (is_array($value)) {
            if (array_keys($value) === range(0, count($value) - 1)) {
                return array_map([$this, 'toObject'], $value);
            }

            $obj = new \stdClass();
            foreach ($value as $k => $v) {
                $obj->$k = $this->toObject($v);
            }
            return $obj;
        }
        return $value;
    }
}
