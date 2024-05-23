<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template T as object
 *
 * @implements Type<T>
 */
final readonly class InstanceOfType implements Type
{
    /**
     * @param class-string<T> $className
     */
    public function __construct(
        public string $className,
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return '\\' . $this->className;
    }

    public function getTypeAnnotation(): string
    {
        return '\\' . $this->className;
    }
}
