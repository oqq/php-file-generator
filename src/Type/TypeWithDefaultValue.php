<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

final readonly class TypeWithDefaultValue implements Type
{
    public function __construct(
        public Type $inner,
        public mixed $value,
    ) {
    }

    public function isOptional(): bool
    {
        return $this->inner->isOptional();
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
