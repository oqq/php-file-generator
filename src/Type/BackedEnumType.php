<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use BackedEnum;
use Oqq\PhpFileGenerator\Type;

/**
 * @template T as BackedEnum
 *
 * @implements Type<T>
 */
final readonly class BackedEnumType implements Type
{
    /**
     * @param class-string<T> $className
     * @param list<non-empty-string> $cases
     */
    public function __construct(
        public string $className,
        public array $cases,
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
