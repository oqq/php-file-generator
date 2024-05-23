<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template Tv
 * @implements Type<list<Tv>>
 */
final readonly class ListType implements Type
{
    /**
     * @param Type<Tv> $valueType
     */
    public function __construct(
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
        return 'list<' . $this->valueType->getTypeAnnotation() . '>';
    }
}
