<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

/**
 * @template-covariant T
 */
interface Type
{
    public function isOptional(): bool;

    /** @return non-empty-string */
    public function getTypeHint(): string;

    /** @return non-empty-string */
    public function getTypeAnnotation(): string;
}
