<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type as NetteType;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification;
use Oqq\PhpFileGenerator\Specification\ClassFunctionSpecification;
use Oqq\PhpFileGenerator\Type;
use Oqq\PhpFileGenerator\Util\Name;

/**
 * @implements CreateFromSpecification<ClassFunctionSpecification>
 */
final readonly class CreateClassFunction implements CreateFromSpecification
{
    public function __invoke(ClassFile $classFile, ClassFunctionSpecification $specification): void
    {
        $namespace = $classFile->getNamespace();

        $class = $classFile->getClass();
        $class->setFinal();
        $class->setReadOnly();

        $this->createConstructorMethod($namespace, $class, $specification);
        $this->createInvokeMethod($namespace, $class, $specification);

        $methods = $class->getMethods();
        \usort($methods, Name::sortMethods(...));

        $class->setMethods($methods);
    }

    private function createConstructorMethod(PhpNamespace $namespace, ClassType $class, ClassFunctionSpecification $specification): void
    {
        $class->removeMethod('__construct');

        if (! $specification->dependencies) {
            return;
        }

        $method = $class->addMethod('__construct');
        $method->setPublic();

        foreach ($specification->dependencies as $parameterName => $parameterType) {
            $parameterName = Name::camelCaseName($parameterName);

            $parameter = $method->addPromotedParameter($parameterName);
            $parameter->setPrivate();

            $this->configureParameter($namespace, $parameter, $parameterType);
        }
    }

    private function createInvokeMethod(PhpNamespace $namespace, ClassType $class, ClassFunctionSpecification $specification): void
    {
        $class->removeMethod('__invoke');

        if (! $specification->parameters && ! $specification->returnType && ! $specification->methodBody) {
            return;
        }

        $method = $class->addMethod('__invoke');
        $method->setPublic();

        $this->setReturnType($method, $specification->returnType);

        foreach ($specification->parameters as $parameterName => $parameterType) {
            $parameterName = Name::camelCaseName($parameterName);
            $parameter = $method->addParameter($parameterName);

            $this->configureParameter($namespace, $parameter, $parameterType);
        }

        if ($specification->methodBody) {
            ($specification->methodBody)($method);
        }
    }

    private function configureParameter(PhpNamespace $namespace, Parameter $parameter, Type $type): void
    {
        if ($type instanceof Type\InstanceOfType) {
            $namespace->addUse($type->className);
        }

        $typeHint = $type->getTypeHint();
        $parameter->setType($typeHint);

        $typeAnnotation = match(\get_class($type)) {
            default => $type->getTypeAnnotation(),
            Type\ListType::class => $namespace->simplifyType($type->getTypeAnnotation()),
        };

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $parameter->setComment('@var ' . $typeAnnotation);
        }
    }

    private function setReturnType(Method $method, ?Type $returnType): void
    {
        if (null === $returnType) {
            $method->setReturnType(NetteType::Void);
            return;
        }

        $typeHint = $returnType->getTypeHint();
        $typeAnnotation = $returnType->getTypeAnnotation();

        $method->setReturnType($typeHint);

        if ($typeAnnotation && $typeAnnotation !== $typeHint) {
            $method->setComment('@return ' . $typeAnnotation);
        }
    }
}
