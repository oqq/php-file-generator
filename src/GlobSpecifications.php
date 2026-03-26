<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Traversable;

final class GlobSpecifications implements Specifications
{
    /** @var array<class-string, Specification> */
    private array $specifications = [];

    /** @var list<PostProcessorSpecification> */
    private array $postProcessorSpecifications = [];

    public static function fromGlobPattern(string $pattern): self
    {
        $specifications = new self();

        $files = \glob($pattern);

        foreach ($specifications->collectFromFiles($files) as $specification) {
            if ($specification instanceof PostProcessorSpecification) {
                $specifications->postProcessorSpecifications []= $specification;
                continue;
            }

            if ($specifications->hasSpecificationFor($specification->className)) {
                throw new \RuntimeException(\sprintf('Duplicate specification for class "%s"', $specification->className));
            }

            $specifications->specifications[$specification->className] = $specification;
        }

        return $specifications;
    }

    /**
     * @param array<string> $files
     * @return iterable<Specification>
     */
    private function collectFromFiles(array $files): iterable
    {
        foreach ($files as $specificationFile) {
            /** @var callable(Specifications):iterable<Specification> $generator */
            $generator = (include $specificationFile);
            yield from $generator($this);
        }
    }

    /**
     * @param class-string $className
     */
    public function hasSpecificationFor(string $className): bool
    {
        return isset($this->specifications[$className]);
    }

    /**
     * @param class-string $className
     */
    public function getSpecificationFor(string $className): Specification
    {
        return $this->specifications[$className] ?? throw new \RuntimeException('no such specification for ' . $className);
    }

    public function getPostProcessorSpecifications(): iterable
    {
        yield from $this->postProcessorSpecifications;
    }

    /**
     * @param class-string $className
     */
    public function getPostProcessorSpecificationsFor(string $className): iterable
    {
        foreach ($this->postProcessorSpecifications as $specification) {
            if ($specification->className === $className) {
                yield $specification;
            }
        }
    }

    public function getIterator(): Traversable
    {
        yield from $this->specifications;
    }
}
