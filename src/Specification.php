<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Goodlive\GoodControl\Domain\AggregateId;

/**
 * @template T of object
 */
interface Specification
{
    /** @var class-string<T> */
    public string $className { get; }

    /**
     * @return non-empty-string
     */
    public function hash(): string;
}
