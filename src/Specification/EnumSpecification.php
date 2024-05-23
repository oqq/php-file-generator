<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\Specification;

/**
 * @template T
 * @implements Specification<T>
 */
final readonly class EnumSpecification implements Specification
{
    public function __construct(
        /** @var class-string<T> */
        public string $className,
        /** @var list<string> */
        public array $cases,
    ) {
    }

    public function hash(): string
    {
        return md5(\implode('.', $this->cases));
    }
}
