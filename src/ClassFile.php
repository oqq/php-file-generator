<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Nette\PhpGenerator\ClassLike;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\EnumType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;

final class ClassFile
{
    /** @var non-empty-string */
    private readonly string $namespaceName;
    /** @var non-empty-string */
    private readonly string $className;

    private ?PhpFile $file = null;
    private ?PhpNamespace $namespace = null;
    private ?ClassLike $class = null;

    public function __construct(
        /** @param class-string $fullClassName */
        public string $fullClassName,
    ) {
        $parts = \explode('\\', $fullClassName);
        $this->className = \array_pop($parts);
        $this->namespaceName = \implode('\\', $parts);
    }

    public static function fromPhpFile(PhpFile $phpFile): self
    {
        $namespace = \current($phpFile->getNamespaces()) ?? throw new \RuntimeException();
        $class = \current($namespace->getClasses()) ?? throw new \RuntimeException();

        $instance = new self($namespace->getName() . '\\' . $class->getName());
        $instance->file = $phpFile;
        $instance->namespace = $namespace;
        $instance->class = $class;

        return $instance;
    }

    public function getFile(): PhpFile
    {
        if (null === $this->file) {
            $this->file = new PhpFile();
        }

        return $this->file;
    }

    public function getNamespace(): PhpNamespace
    {
        if (null === $this->namespace) {
            $this->namespace = $this->getFile()->addNamespace($this->namespaceName);
        }

        return $this->namespace;
    }

    public function getClass(): ClassType
    {
        if (null === $this->class) {
            $this->class = $this->getNamespace()->addClass($this->className);
        }

        return $this->class;
    }

    public function getEnum(): EnumType
    {
        if (null === $this->class) {
            $this->class = $this->getNamespace()->addEnum($this->className);
        }

        return $this->class;
    }

    public function addImport(string $fullClassName): void
    {
        $this->getNamespace()->addUse($fullClassName);
    }

    public function addImportForType(Type $type): void
    {
        if ($type instanceof Type\TypeWithDefaultValue) {
            $type = $type->inner;
        }

        if ($type instanceof Type\NullableType) {
            $type = $type->inner;
        }

        if ($type instanceof Type\ListType) {
            $type = $type->valueType;
        }

        if ($type instanceof Type\InstanceOfType) {
            $this->addImport($type->className);
        }
    }
}
