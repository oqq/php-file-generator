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
            $this->addParameter($classFile, $method, 'value', $type);
            return;
        }

        foreach ($type->elements as $elementName => $elementType) {
            $classFile->addImportForType($elementType);

            $parameterName = Name::camelCaseName($elementName);

            if ($elementType instanceof Type\TypeWithFixedValue) {
                $this->addProperty($classFile, $method, $parameterName, $elementType);
                continue;
            }

            if ($elementType->isOptional()) {
                $this->addLazyProperty($classFile, $parameterName, $elementType);
                continue;
            }

            $this->addParameter($classFile, $method, $parameterName, $elementType);
        }
    }

    private function addParameter(ClassFile $classFile, Method $method, string $parameterName, Type $type): void
    {
        $parameter = $method->addPromotedParameter($parameterName);

        if ($type instanceof Type\TypeWithDefaultValue) {
            $parameter->setDefaultValue($type->value);
            $type = $type->inner;
        }

        $typeHint = $type->getTypeHint();
        $parameter->setType($typeHint);

        if ($type->isOptional()) {
            $parameter->setNullable();
        }

        $typeAnnotation = $type->getTypeAnnotation();

        if ($type instanceof Type\ListType && $type->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $classFile->getNamespace()->simplifyType($type->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $parameter->setComment('@var ' . $typeAnnotation);
        }
    }

    private function addProperty(ClassFile $classFile, Method $method, string $propertyName, Type\TypeWithFixedValue $type): void
    {
        $class = $classFile->getClass();
        $class->removeProperty($propertyName);

        $method->addBody('$this->? = ?;', [$propertyName, $type->value]);

        $property = $class->addProperty($propertyName);
        $innerType = $type->inner;

        $typeHint = $innerType->getTypeHint();
        $property->setType($typeHint);

        if ($innerType->isOptional()) {
            $property->setNullable();
        }

        $typeAnnotation = $innerType->getTypeAnnotation();

        if ($innerType instanceof Type\ListType && $innerType->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $classFile->getNamespace()->simplifyType($innerType->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $property->setComment('@var ' . $typeAnnotation);
        }
    }

    private function addLazyProperty(ClassFile $classFile, string $propertyName, Type $type): void
    {
        $class = $classFile->getClass();
        $class->removeProperty($propertyName);

        $property = $class->addProperty($propertyName);
        $innerType = $type->inner;

        $typeHint = $innerType->getTypeHint();
        $property->setType($typeHint);

        if ($innerType->isOptional()) {
            $property->setNullable();
        }

        $typeAnnotation = $innerType->getTypeAnnotation();

        if ($innerType instanceof Type\ListType && $innerType->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $classFile->getNamespace()->simplifyType($innerType->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $property->setComment('@var ' . $typeAnnotation);
        }
    }
}
