<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template Tk
 * @template Tv
 *
 * @implements Type<iterable<Tk, Tv>>
 */
final readonly class IterableType implements Type
{
    /**
     * @param Type<Tk> $keyType
     * @param Type<Tv> $valueType
     */
    public function __construct(
        private Type $keyType,
        private Type $valueType
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'iterable';
    }

    public function getTypeAnnotation(): string
    {
        return 'iterable<' . $this->keyType->getTypeAnnotation() . ', ' . $this->valueType->getTypeAnnotation() . '>';
    }
}
