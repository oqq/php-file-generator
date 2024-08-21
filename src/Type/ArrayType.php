<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<array>
 */
final readonly class ArrayType implements Type
{
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
        return 'array';
    }
}
