<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<array-key>
 */
final readonly class ArrayKeyType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'int | string';
    }

    public function getTypeAnnotation(): string
    {
        return 'array-key';
    }
}
