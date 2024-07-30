<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Specification;

use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
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

    /** @var iterable<non-empty-string, Type> */
    public iterable $classProperties = [];

    /** @var iterable<non-empty-string, CreateMethodBody> */
    public iterable $classMethods = [];

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

    /**
     * @param iterable<non-empty-string, Type> $properties
     */
    public function withProperties(iterable $properties): self
    {
        $clone = clone $this;
        $clone->classProperties = $properties;

        return $clone;
    }

    /**
     * @param iterable<non-empty-string, CreateMethodBody> $methods
     */
    public function withMethods(iterable $methods): self
    {
        $clone = clone $this;
        $clone->classMethods = $methods;

        return $clone;
    }
}
