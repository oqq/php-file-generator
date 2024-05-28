<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @implements Type<class-string>
 */
final readonly class ClassStringType implements Type
{
    /**
     * @param ?class-string $type
     */
    public function __construct(
        private ?string $type = null,
    ) {
    }

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
        if ($this->type === null) {
            return 'class-string';
        }

        return 'class-string<\\' . $this->type . '>';
    }
}
