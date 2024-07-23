<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Type as NetteType;
use Nette\PhpGenerator\Type as NettType;
use Oqq\PhpFileGenerator\Specification\PostProcessorSpecification;
use Oqq\PhpFileGenerator\Type\ClassStringType;
use Oqq\PhpFileGenerator\Type\TypeWithDefaultValue;

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
}
