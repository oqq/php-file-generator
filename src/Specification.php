<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

/**
 * @template T of object
 * @property-read class-string<T> $className
 */
interface Specification
{
    /**
     * @return non-empty-string
     */
    public function hash(): string;
}
