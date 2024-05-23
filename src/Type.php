<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

/**
 * @template-covariant T
 */
interface Type
{
    /** @return non-empty-string */
    public function getTypeHint(): string;

    /** @return non-empty-string */
    public function getTypeAnnotation(): string;
}
