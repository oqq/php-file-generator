<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification;

/**
 * @template T
 * @implements Specification<T>
 */
final readonly class UnitTestSpecification implements Specification
{
    public function __construct(
        /** @var class-string<T> */
        public string $className,
        /** @var class-string */
        public string $testedClassName,
        /** @var array<non-empty-string, class-string> */
        public array $mocks,
        /** @var array<non-empty-string, CreateMethodBody> */
        public array $testMethods,
    ) {
    }

    public function hash(): string
    {
        $values = [];

        $values[] = \serialize($this->mocks);
        $values[] = \serialize($this->testedClassName);

        return \md5(\implode('.', $values));
    }
}
