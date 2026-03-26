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

final readonly class DatabaseSchemaPostgresBody implements CreateMethodBody
{
    public function __construct(
        /** @var list<class-string> */
        private array $readModels,
        private Specifications $specifications,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $method->addBody('$table = Schema\Table::editor();');
        $method->addBody('$table->setUnquotedName(self::TABLE_NAME);');
        $method->addBody('');

        $columns = $this->matchColumns($this->readModels);

        foreach ($columns as $name => [$doctrineType, $doctrineOptions]) {
            $method->addBody('$column = Schema\Column::editor();');
            $method->addBody('$column->setUnquotedName(?);', [$name]);
            $method->addBody('$column->setTypeName(?);', [$doctrineType]);

            foreach ($doctrineOptions as $option => $value) {
                $method->addBody('$column->?(?);', [$option, $value]);
            }

            $method->addBody('$table->addColumn($column->create());');
            $method->addBody('');
        }

        $method->addBody(<<<'PHP'
            $table->setPrimaryKeyConstraint(
                Schema\PrimaryKeyConstraint::editor()
                ->setUnquotedColumnNames(?)
                ->create()
            );
            PHP
            , [\array_key_first($columns)]);

        $method->addBody('');
        $method->addBody('return $table->create();');
    }

    public function hash(): string
    {
        return \md5(\serialize($this->readModels));
    }

    /**
     * @param list<class-string> $readModels
     * @return array<non-empty-string, list{Literal, array<non-empty-string, mixed>}>
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
                        \sprintf('Mismatching element type for %s, %s != %s', $name, $elements[$name]::class, $elementType::class)
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
        $options = [];

        if ($type instanceof Type\TypeWithDefaultValue) {
            $options = [...$options, ...['setDefaultValue' => $type->value]];
            $type = $type->inner;
        }

        if ($type instanceof Type\NullableType) {
            $options = [...$options, ...['setNotNull' => false, 'setDefaultValue' => null]];
            $type = $type->inner;
        }

        if ($type instanceof Type\OptionalType) {
            $options = [...$options, ...['setNotNull' => false, 'setDefaultValue' => null]];
            $type = $type->inner;
        }

        return match ($type::class) {
            Type\UuidType::class => [new Literal('Types::GUID'), [...$options]],
            Type\BooleanType::class => [new Literal('Types::BOOLEAN'), [...$options]],
            Type\ArrayType::class,
            Type\DictType::class,
            Type\ShapeType::class => [new Literal('Types::JSONB'), ['setDefaultValue' => '{}', ...$options]],
            Type\ListType::class => [new Literal('Types::JSONB'), ['setDefaultValue' => '[]', ...$options]],
            Type\IntegerType::class,
            Type\NaturalType::class,
            Type\PositiveIntegerType::class => [new Literal('Types::INTEGER'), [...$options]],
            Type\InstanceOfType::class => [new Literal('Types::JSONB'), [...$options]],
            default => [new Literal('Types::STRING'), [...$options]],
        };
    }
}
