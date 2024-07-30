<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use IteratorAggregate;
use Oqq\PhpFileGenerator\Specification\PostProcessorSpecification;

/**
 * @extends IteratorAggregate<class-string, Specification>
 */
interface Specifications extends IteratorAggregate
{
    /**
     * @param class-string $className
     */
    public function hasSpecificationFor(string $className): bool;

    /**
     * @template T
     * @param class-string<T> $className
     * @return Specification<T>
     */
    public function getSpecificationFor(string $className): Specification;

    /**
     * @return iterable<array-key, PostProcessorSpecification>
     */
    public function getPostProcessorSpecifications(): iterable;

    /**
     * @template T
     * @param class-string<T> $className
     * @return iterable<array-key, PostProcessorSpecification<T>>
     */
    public function getPostProcessorSpecificationsFor(string $className): iterable;
}
