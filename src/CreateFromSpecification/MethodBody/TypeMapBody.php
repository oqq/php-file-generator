<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification;
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


        $method->setBody('return ?;', [$result]);
    }

    public function hash(): string
    {
        return $this->getSpecification($this->valueObject)->hash();
    }

    private function getTypeMap(Type $type): array | string
    {
        if ($type instanceof Type\NullableType) {
            $type = $type->inner;
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
            return $this->getTypeMap($this->getSpecification($type->className)->type);
        }

        return match(\get_class($type)) {
            default => 'word',
            Type\NaturalType::class => 'randomNumber',
            Type\UuidType::class => 'uuid',
            Type\BooleanType::class => 'boolean',
        };
    }

    private function getRecursiveTypeMap(Type\ShapeType $type): array
    {
        return \array_map(fn (Type $type): string | array => $this->getTypeMap($type), $type->elements);
    }

    private function getSpecification(string $className): Specification
    {
        return $this->specifications->getSpecificationFor($className);
    }
}
