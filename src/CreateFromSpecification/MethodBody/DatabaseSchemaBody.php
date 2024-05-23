<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Specifications;
use Oqq\PhpFileGenerator\TypeBuilder;
use Oqq\PhpFileGenerator\Type;

final readonly class DatabaseSchemaBody implements CreateMethodBody
{
    public function __construct(
        /** @var list<class-string> */
        private array $readModels,
        private Specifications $specifications,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $method->addBody('$schema = new Schema();');
        $method->addBody('');

        $method->addBody('$table = $schema->createTable(self::TABLE_NAME);');
        $method->addBody('');

        $columns = $this->matchColumns($this->readModels);

        foreach ($columns as $name => [$doctrineType, $doctrineOptions]) {
            $method->addBody('$table->addColumn(?, ?, ?);', [$name, $doctrineType, $doctrineOptions]);
        }

        $method->addBody('');
        $method->addBody('$table->setPrimaryKey([?]);', [\array_key_first($columns)]);

        $method->addBody('');
        $method->addBody('return $schema;');
    }

    public function hash(): string
    {
        return \md5(\serialize($this->readModels));
    }

    /**
     * @param list<class-string> $readModels
     * @return array<non-empty-string, list{Literal, array}>
     */
    private function matchColumns(array $readModels): array
    {
        /** @var array<non-empty-string, TypeBuilder> $elements */
        $elements = [];

        /** @var array<non-empty-string, Literal> $columns */
        $columns = [];

        foreach ($readModels as $readModel) {
            $specification = $this->specifications->getSpecificationFor($readModel);

            if (null === $specification) {
                throw new \RuntimeException('specification for read model not defined: ' . $readModel);
            }

            if (false === $specification instanceof ValueObjectSpecification) {
                throw new \RuntimeException('not sure how to handle specification');
            }

            $specificationType = $specification->type;

            if (false === $specificationType instanceof Type\ShapeType) {
                throw new \RuntimeException('not sure how to handle type');
            }

            foreach ($specificationType->elements as $name => $elementType) {

                if (isset($elements[$name])) {
                    if ($elements[$name]::class === $elementType::class) {
                        continue;
                    }

                    throw new \RuntimeException(
                        \sprintf('Mismatching element type, %s != %s', $elements[$name]::class, $elementType::class)
                    );
                }

                $elements[$name] = $elementType;
            }
        }

        foreach ($elements as $name => $elementType) {
            $columns[$name] = $this->generatorTypeToDoctrineType($elementType);
        }

        return $columns;
    }

    /**
     * @return list{Literal, array<non-empty-string, mixed>}
     */
    private function generatorTypeToDoctrineType(Type $type): array
    {
        return match ($type::class) {
            Type\UuidType::class => [new Literal('Types::GUID'), []],
            Type\BooleanType::class => [new Literal('Types::BOOLEAN'), []],
            Type\ShapeType::class,
            Type\ListType::class => [new Literal('Types::JSON'), []],
            default => [new Literal('Types::STRING'), ['length' => 1000]],
        };
    }
}
