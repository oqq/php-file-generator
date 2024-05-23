<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<non-empty-string>
 */
final readonly class NonEmptyStringType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'string';
    }

    public function getTypeAnnotation(): string
    {
        return 'non-empty-string';
    }
}
