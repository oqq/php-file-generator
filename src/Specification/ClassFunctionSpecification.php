<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification;
use Oqq\PhpFileGenerator\Type;

/**
 * @template T
 * @implements Specification<T>
 */
final readonly class ClassFunctionSpecification implements Specification
{
    public function __construct(
        /** @var class-string<T> */
        public string $className,
        /** @var array<non-empty-string, Type> */
        public array $dependencies,
        /** @var array<non-empty-string, Type> */
        public array $parameters,
        public ?Type $returnType = null,
        public ?CreateMethodBody $methodBody = null,
    ) {
    }

    public function hash(): string
    {
        $values = [];

        $values[] = \serialize($this->dependencies);
        $values[] = \serialize($this->parameters);
        $values[] = \serialize($this->returnType?->getTypeAnnotation());
        $values[] = \serialize($this->methodBody?->hash());

        return \md5(\implode('.', $values));
    }
}
