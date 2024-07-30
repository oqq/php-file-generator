<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template Tk of array-key
 * @template Tv
 *
 * @implements Type<array<Tk, Tv>>
 */
final readonly class DictType implements Type
{
    /**
     * @param Type<Tk> $keyType
     * @param Type<Tv> $valueType
     */
    public function __construct(
        public Type $keyType,
        public Type $valueType,
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'array';
    }

    public function getTypeAnnotation(): string
    {
        return 'array<' . $this->keyType->getTypeAnnotation() . ', ' . $this->valueType->getTypeAnnotation() . '>';
    }
}
