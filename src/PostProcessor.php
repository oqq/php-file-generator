<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type as NetteType;
use Nette\PhpGenerator\Type as NettType;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification\PostProcessorSpecification;
use Oqq\PhpFileGenerator\Type\ClassStringType;
use Oqq\PhpFileGenerator\Type\TypeWithDefaultValue;
use Oqq\PhpFileGenerator\Type\TypeWithFixedValue;

final readonly class PostProcessor
{
    public function __invoke(ClassFile $classFile, PostProcessorSpecification $specification): void
    {
        $namespace = $classFile->getNamespace();
        $class = $classFile->getClass();

        $this->processImports($namespace, $specification->imports);
        $this->processClassAnnotations($namespace, $class, $specification->classAnnotations);
        $this->processImplements($namespace, $class, $specification->implements);
        $this->processClassConstants($namespace, $class, $specification->classConstants);
        $this->processClassProperties($namespace, $class, $specification->classProperties);
        $this->processClassMethods($namespace, $class, $specification->classMethods);
    }

    /**
     * @param list<class-string> $imports
     */
    private function processImports(PhpNamespace $namespace, array $imports): void
    {
        foreach ($imports as $import) {
            $namespace->addUse($import);
        }
    }

    /**
     * @param list<non-empty-string> $classAnnotations
     */
    private function processClassAnnotations(PhpNamespace $namespace, ClassType $class, array $classAnnotations): void
    {
        $class->setComment(null);

        foreach ($classAnnotations as $classAnnotation) {
            $class->addComment($namespace->simplifyType($classAnnotation));
        }
    }

    /**
     * @param list<non-empty-string> $implements
     */
    private function processImplements(PhpNamespace $namespace, ClassType $class, array $implements): void
    {
        if (!$implements) {
            return;
        }

        foreach ($implements as $implement) {
            $namespace->addUse($implement);
        }

        $class->setImplements($implements);
    }

    /**
     * @param iterable<non-empty-string, TypeWithDefaultValue> $constants
     */
    private function processClassConstants(PhpNamespace $namespace, ClassType $class, iterable $constants): void
    {
        if (!$constants) {
            return;
        }

        foreach ($constants as $name => $type) {
            $constant = $class->addConstant($name, $type->value, overwrite: true);
            $constant->setPublic();

            $typeHint = $type->getTypeHint();
            $constant->setType($typeHint);

            $typeAnnotation = $type->getTypeAnnotation();

            if ($typeAnnotation && $typeAnnotation !== $typeHint) {
                $constant->setComment('@var ' . $typeAnnotation);
            }
        }
    }

    /**
     * @param iterable<non-empty-string, Type> $properties
     */
    private function processClassProperties(PhpNamespace $namespace, ClassType $class, iterable $properties): void
    {
        if (!$properties) {
            return;
        }

        foreach ($properties as $name => $type) {
            $value = $type instanceof TypeWithDefaultValue || $type instanceof TypeWithFixedValue
                ? $type->value
                : null;

            $property = $class->addProperty($name, $value, overwrite: true);
            $property->setPrivate();

            $typeHint = $type->getTypeHint();
            $property->setType($typeHint);

            $typeAnnotation = $type->getTypeAnnotation();

            if ($typeAnnotation && $typeAnnotation !== $typeHint) {
                $property->setComment('@var ' . $typeAnnotation);
            }
        }
    }

    /**
     * @param iterable<non-empty-string, CreateMethodBody> $methods
     */
    private function processClassMethods(PhpNamespace $namespace, ClassType $class, iterable $methods): void
    {
        if (!$methods) {
            return;
        }

        foreach ($methods as $name => $createBody) {
            $method = $class->addMethod($name, overwrite: true);
            $createBody($method);
        }
    }
}
