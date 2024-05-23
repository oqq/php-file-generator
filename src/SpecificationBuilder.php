<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;

final readonly class SpecificationBuilder
{
    /**
     * @template T
     *
     * @param class-string<T> $className
     *
     * @return Specification\PostProcessorSpecification<T>
     */
    public static function postProcessor(string $className): Specification\PostProcessorSpecification
    {
        return new Specification\PostProcessorSpecification($className);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     *
     * @return iterable<Specification<T>>
     */
    public static function valueObject(string $className, Type $type): iterable
    {
        yield new Specification\ValueObjectSpecification($className, $type);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param list<string> $cases
     *
     * @return iterable<Specification<T>>
     */
    public static function enum(string $className, array $cases): iterable
    {
        yield new Specification\EnumSpecification($className, $cases);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param non-empty-string $tableName
     * @param list<class-string> $readModels
     *
     * @return iterable<Specification<T>>
     */
    public static function databaseSchema(string $className, string $tableName, array $readModels): iterable
    {
        yield new Specification\DatabaseSchemaSpecification($className, $tableName, $readModels);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param array<non-empty-string, Type> $dependencies
     * @param array<non-empty-string, Type> $parameters
     *
     * @return iterable<Specification<T>>
     */
    public static function classFunction(
        string $className,
        array $dependencies = [],
        array $parameters = [],
        ?Type $returnType = null,
        ?CreateMethodBody $methodBody = null,
    ): iterable {
        yield new Specification\ClassFunctionSpecification($className, $dependencies, $parameters, $returnType, $methodBody);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param class-string $testedClassName
     * @param array<non-empty-string, class-string> $mocks
     * @param array<non-empty-string, CreateMethodBody> $testMethods
     *
     * @return iterable<Specification<T>>
     */
    public static function unitTest(string $className, string $testedClassName, array $mocks = [], array $testMethods = []): iterable
    {
        yield new Specification\UnitTestSpecification($className, $testedClassName, $mocks, $testMethods);
    }
}
