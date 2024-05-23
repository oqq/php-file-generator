<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<bool>
 */
final readonly class BooleanType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'bool';
    }

    public function getTypeAnnotation(): string
    {
        return 'bool';
    }
}
