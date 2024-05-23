<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<bool>
 */
final readonly class NaturalType implements Type
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
        return 'non-negative-int';
    }
}
