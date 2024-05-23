<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

final readonly class MethodCall
{
    public function __construct(
        /** @var class-string */
        public string $className,
        /** @var non-empty-string */
        public array $methodName,
        /** @var array<non-empty-string> */
        public array $parameters,
    ) {
    }
}
