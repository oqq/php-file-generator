<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\Specification;
use Oqq\PhpFileGenerator\Type;

/**
 * @template T
 * @implements Specification<T>
 */
final readonly class ValueObjectSpecification implements Specification
{
    public function __construct(
        /** @param class-string<T> $className */
        public string $className,
        public Type $type,
    ) {
    }

    public function hash(): string
    {
        return md5($this->type->getTypeAnnotation());
    }
}
