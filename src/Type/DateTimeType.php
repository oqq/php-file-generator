<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use DateTimeImmutable;
use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<DateTimeImmutable>
 */
final readonly class DateTimeType implements Type
{
    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return '\\DateTimeImmutable';
    }

    public function getTypeAnnotation(): string
    {
        return '\\DateTimeImmutable';
    }
}
