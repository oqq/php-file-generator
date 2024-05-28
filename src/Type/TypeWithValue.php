<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

final readonly class TypeWithValue implements Type
{
    public function __construct(
        public Type $innerType,
        public mixed $value,
    ) {
    }

    public function isOptional(): bool
    {
        return $this->innerType->isOptional();
    }

    public function getTypeHint(): string
    {
        return $this->innerType->getTypeHint();
    }

    public function getTypeAnnotation(): string
    {
        return $this->innerType->getTypeAnnotation();
    }
}
