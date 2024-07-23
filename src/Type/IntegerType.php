<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<int>
 */
final readonly class IntegerType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'int';
    }

    public function getTypeAnnotation(): string
    {
        return 'int';
    }
}
