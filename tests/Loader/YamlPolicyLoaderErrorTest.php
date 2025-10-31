<?php

declare(strict_types=1);

namespace Tests\Loader;

use PHPUnit\Framework\TestCase;
use R4Policy\Loader\YamlPolicyLoader;
use R4Policy\Validator\PolicyValidator;
use RuntimeException;

final class YamlPolicyLoaderErrorTest extends TestCase
{
    public function testThrowsWhenFileDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/does not exist/');

        $loader = new YamlPolicyLoader(new PolicyValidator());
        $loader->load('/nonexistent/path/file.yaml');
    }
}
