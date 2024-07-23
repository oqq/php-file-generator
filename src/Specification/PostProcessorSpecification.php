<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\Specification;
use Oqq\PhpFileGenerator\Type\TypeWithDefaultValue;

/**
 * @template T
 * @implements Specification<T>
 */
final class PostProcessorSpecification implements Specification
{
    /** @var list<class-string> */
    public array $imports = [];

    /** @var list<non-empty-string> */
    public array $classAnnotations = [];

    /** @var list<class-string> */
    public array $implements = [];

    /** @var iterable<non-empty-string, TypeWithDefaultValue> */
    public iterable $classConstants = [];

    public function __construct(
        /** @param class-string<T> $className */
        public string $className,
    ) {
    }

    public function hash(): never
    {
        throw new \RuntimeException();
    }

    /**
     * @param list<class-string> $imports
     */
    public function withImports(array $imports): self
    {
        $clone = clone $this;
        $clone->imports = $imports;

        return $clone;
    }

    /**
     * @param list<non-empty-string> $annotations
     */
    public function withClassAnnotations(array $annotations): self
    {
        $clone = clone $this;
        $clone->classAnnotations = $annotations;

        return $clone;
    }

    /**
     * @param list<class-string> $interfaces
     */
    public function withImplements(array $interfaces): self
    {
        $clone = clone $this;
        $clone->implements = $interfaces;

        return $clone;
    }

    /**
     * @param iterable<non-empty-string, TypeWithDefaultValue> $constants
     */
    public function withConstants(iterable $constants): self
    {
        $clone = clone $this;
        $clone->classConstants = $constants;

        return $clone;
    }
}
