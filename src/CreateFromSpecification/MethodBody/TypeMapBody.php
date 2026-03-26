<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Goodlive\GoodControl\Generator\Specification\DomainAggregateIdSpecification;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification;
use Oqq\PhpFileGenerator\Specification\EnumSpecification;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Specifications;
use Oqq\PhpFileGenerator\Type;

final readonly class TypeMapBody implements CreateMethodBody
{
    public function __construct(
        /** @var class-string */
        private string $valueObject,
        private Specifications $specifications,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $specification = $this->specifications->getSpecificationFor($this->valueObject);

        if (false === $specification instanceof ValueObjectSpecification) {
            throw new \RuntimeException('not sure how to handle specification');
        }

        $type = $specification->type;
        $result = $this->getTypeMap($type);

        if (\is_string($result)) {
            $result = ['value' => $result];
        }

        $method->setBody('return ?;', [$result]);
    }

    public function hash(): string
    {
        return $this->getSpecification($this->valueObject)->hash();
    }

    private function getTypeMap(Type $type): array | string | Literal
    {
        if ($type instanceof Type\OptionalType) {
            $type = $type->inner;
        }

        if ($type instanceof Type\TypeWithFixedValue) {
            $type = $type->inner;
        }

        if ($type instanceof Type\TypeWithDefaultValue) {
            $type = $type->inner;
        }

        if ($type instanceof Type\NullableType) {
            $type = $type->inner;
        }

        if ($type instanceof Type\DictType) {
            return [
                $this->getTypeMap($type->keyType) =>
                $this->getTypeMap($type->valueType),
            ];
        }

        if ($type instanceof Type\ShapeType) {
            return $this->getRecursiveTypeMap($type);
        }

        if ($type instanceof Type\ListType) {
            return [
                $this->getTypeMap($type->valueType),
                $this->getTypeMap($type->valueType),
            ];
        }

        if ($type instanceof Type\InstanceOfType) {
            $specification = $this->getSpecification($type->className);
            
            if ($specification instanceof ValueObjectSpecification) {
                return $this->getTypeMap($specification->type);
            }

            if ($specification instanceof EnumSpecification) {
                return new Literal(
                    \sprintf("fn() => ['f#randomElement', \\%s::cases()]", $type->className),
                );
            }
        }

        if ($type instanceof Type\BackedEnumType) {
            return new Literal(
                \sprintf("fn() => ['f#randomElement', \\%s::cases()]", $type->className),
            );
        }

        return match(\get_class($type)) {
            default => 'f#word',
            Type\ArrayType::class => [],
            Type\BooleanType::class => 'f#boolean',
            Type\IntegerType::class,
            Type\NaturalType::class,
            Type\PositiveIntegerType::class => 'f#randomNumber',
            Type\UuidType::class => 'f#uuid',
            Type\DateTimeType::class => 'f#date,Y-m-d\TH:i:s.uP',
        };
    }

    private function getRecursiveTypeMap(Type\ShapeType $type): array
    {
        return \array_map(fn (Type $type): string | array | Literal => $this->getTypeMap($type), $type->elements);
    }

    private function getSpecification(string $className): Specification
    {
        return $this->specifications->getSpecificationFor($className);
    }
}
