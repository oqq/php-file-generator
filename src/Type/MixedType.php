<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<mixed>
 */
final readonly class MixedType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'mixed';
    }

    public function getTypeAnnotation(): string
    {
        return 'mixed';
    }
}
