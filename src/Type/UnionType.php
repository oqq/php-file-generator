<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template TL
 * @template TR
 * @implements Type<TL|TR>
 */
final readonly class UnionType implements Type
{
    /**
     * @param Type<TL> $leftType
     * @param Type<TR> $rightType
     */
    public function __construct(
        public Type $leftType,
        public Type $rightType,
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return $this->leftType->getTypeHint() . '|' . $this->rightType->getTypeHint();
    }

    public function getTypeAnnotation(): string
    {
        return $this->leftType->getTypeAnnotation() . '|' . $this->rightType->getTypeAnnotation();
    }
}
