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
        $this->importTypes($classFile, $specification->classConstants);
        $this->processImports($classFile, $specification->implements);

        $this->processImports($classFile, $specification->imports);
        $this->processClassAnnotations($classFile, $specification->classAnnotations);
        $this->processImplements($classFile, $specification->implements);
        $this->processClassConstants($classFile, $specification->classConstants);
        $this->processClassProperties($classFile, $specification->classProperties);
        $this->processClassMethods($classFile, $specification->classMethods);
    }

    /**
     * @param iterable<Type> $types
     * @return void
     */
    private function importTypes(ClassFile $classFile, iterable $types)
    {
        foreach ($types as $type) {
            $classFile->addImportForType($type);
        }
    }

    /**
     * @param list<class-string> $imports
     */
    private function processImports(ClassFile $classFile, array $imports): void
    {
        foreach ($imports as $import) {
            $classFile->addImport($import);
        }
    }

    /**
     * @param list<non-empty-string> $classAnnotations
     */
    private function processClassAnnotations(ClassFile $classFile, array $classAnnotations): void
    {
        $class = $classFile->getClass();
        $class->setComment(null);

        foreach ($classAnnotations as $classAnnotation) {
            $class->addComment($classFile->getNamespace()->simplifyType($classAnnotation));
        }
    }

    /**
     * @param list<non-empty-string> $implements
     */
    private function processImplements(ClassFile $classFile, array $implements): void
    {
        if (!$implements) {
            return;
        }

        $class = $classFile->getClass();
        $class->setImplements($implements);
    }

    /**
     * @param iterable<non-empty-string, TypeWithDefaultValue> $constants
     */
    private function processClassConstants(ClassFile $classFile, iterable $constants): void
    {
        if (!$constants) {
            return;
        }

        $class = $classFile->getClass();

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
    private function processClassProperties(ClassFile $classFile, iterable $properties): void
    {
        if (!$properties) {
            return;
        }

        $class = $classFile->getClass();

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
    private function processClassMethods(ClassFile $classFile, iterable $methods): void
    {
        if (!$methods) {
            return;
        }

        $class = $classFile->getClass();

        foreach ($methods as $name => $createBody) {
            $method = $class->addMethod($name, overwrite: true);
            $createBody($method);
        }
    }
}
