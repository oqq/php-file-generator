<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Type;
use Oqq\PhpFileGenerator\Util\Name;

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

        $type = $specification->type;

        if (false === $type instanceof Type\ShapeType) {
            $this->addParameter($namespace, $method, 'value', $type);
            return;
        }

        foreach ($type->elements as $elementName => $elementType) {
            $parameterName = Name::camelCaseName($elementName);
            $this->addParameter($namespace, $method, $parameterName, $elementType);
        }
    }

    private function addParameter(PhpNamespace $namespace, Method $method, string $parameterName, Type $type): void
    {
        $parameter = $method->addPromotedParameter($parameterName);

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

        $typeAnnotation = $type->getTypeAnnotation();

        if ($type instanceof Type\ListType && $type->valueType instanceof Type\InstanceOfType) {
            $typeAnnotation = $namespace->simplifyType($type->getTypeAnnotation());
        }

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $parameter->setComment('@var ' . $typeAnnotation);
        }
    }
}
