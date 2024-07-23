<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type as NetteType;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Type;
use Oqq\PhpFileGenerator\Util\Name;

use function Psl\Type\null;

/**
 * @implements CreateFromSpecification<ValueObjectSpecification>
 */
final readonly class CreateValueObject implements CreateFromSpecification
{
    public function __invoke(ClassFile $classFile, ValueObjectSpecification $specification): void
    {
        $namespace = $classFile->getNamespace();

        $class = $classFile->getClass();
        $class->setFinal();
        $class->setReadOnly();

        $class->removeMethod('__construct');

        $method = $class->addMethod('__construct');
        $method->setPublic();
        $method->setBody('');

        $type = $specification->type;

        if (false === $type instanceof Type\ShapeType) {
            $this->addParameter($namespace, $method, 'value', $type);
            return;
        }

        foreach ($type->elements as $elementName => $elementType) {
            $parameterName = Name::camelCaseName($elementName);

            if ($elementType instanceof Type\TypeWithFixedValue) {
                $this->addProperty($namespace, $class, $method, $parameterName, $elementType);
                continue;
            }

            $this->addParameter($namespace, $method, $parameterName, $elementType);
        }
    }

    private function addParameter(PhpNamespace $namespace, Method $method, string $parameterName, Type $type): void
    {
        $parameter = $method->addPromotedParameter($parameterName);

        if ($type instanceof Type\TypeWithDefaultValue) {
            $parameter->setDefaultValue($type->value);
            $type = $type->inner;
        }

        if ($type instanceof Type\InstanceOfType) {
            $namespace->addUse($type->className);
        }

        if ($type instanceof Type\NullableType && $type->inner instanceof Type\InstanceOfType) {
            $namespace->addUse($type->inner->className);
        }

        if ($type instanceof Type\ListType && $type->valueType instanceof Type\InstanceOfType) {
            $namespace->addUse($type->valueType->className);
        }

        $typeHint = $type->getTypeHint();
        $parameter->setType($typeHint);

        if ($type->isOptional()) {
            $parameter->setNullable();
        }

        $typeAnnotation = $type->getTypeAnnotation();

        if ($type instanceof Type\ListType && $type->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $namespace->simplifyType($type->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $parameter->setComment('@var ' . $typeAnnotation);
        }
    }

    private function addProperty(PhpNamespace $namespace, ClassType $class, Method $method, string $propertyName, Type\TypeWithFixedValue $type): void
    {
        $method->addBody('$this->? = ?;', [$propertyName, $type->value]);

        $class->removeProperty($propertyName);

        $property = $class->addProperty($propertyName);
        $innerType = $type->inner;

        if ($innerType instanceof Type\InstanceOfType) {
            $namespace->addUse($innerType->className);
        }

        if ($innerType instanceof Type\NullableType && $innerType->inner instanceof Type\InstanceOfType) {
            $namespace->addUse($innerType->inner->className);
        }

        if ($innerType instanceof Type\ListType && $innerType->valueType instanceof Type\InstanceOfType) {
            $namespace->addUse($innerType->valueType->className);
        }

        $typeHint = $innerType->getTypeHint();
        $property->setType($typeHint);

        if ($innerType->isOptional()) {
            $property->setNullable();
        }

        $typeAnnotation = $innerType->getTypeAnnotation();

        if ($innerType instanceof Type\ListType && $innerType->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $namespace->simplifyType($innerType->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $property->setComment('@var ' . $typeAnnotation);
        }
    }
}
