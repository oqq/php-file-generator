<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Oqq\PhpFileGenerator\Specification\PostProcessorSpecification;
use Traversable;

final class GlobSpecifications implements Specifications
{
    /** @var array<class-string, Specification> */
    private array $specifications = [];

    /** @var array<class-string, PostProcessorSpecification> */
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

    public function hasSpecificationFor(string $className): bool
    {
        return isset($this->specifications[$className]);
    }

    public function getSpecificationFor(string $className): Specification
    {
        return $this->specifications[$className] ?? throw new \RuntimeException('no such specification for ' . $className);
    }

    public function getPostProcessorSpecifications(): iterable
    {
        yield from $this->postProcessorSpecifications;
    }

    public function getIterator(): Traversable
    {
        yield from $this->specifications;
    }
}
