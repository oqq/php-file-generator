<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template T
 * @implements Type<T>
 */
final readonly class OptionalType implements Type
{
    /**
     * @param Type<T> $inner
     */
    public function __construct(
        public Type $inner,
    ) {
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function getTypeHint(): string
    {
        return $this->inner->getTypeHint();
    }

    public function getTypeAnnotation(): string
    {
        return $this->inner->getTypeAnnotation();
    }
}
