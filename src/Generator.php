<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

final readonly class Generator
{
    public function __construct(
        private FileStorage $fileStorage,
        private Specifications $specifications,
        private string $cacheFile,
    ) {
    }

    public function generate(): void
    {
        $cacheContent = \is_file($this->cacheFile) ? \file_get_contents($this->cacheFile) : '{}';
        $cache = \json_decode($cacheContent, associative: true, flags: \JSON_THROW_ON_ERROR);

        $creators = [
            Specification\ValueObjectSpecification::class => new CreateFromSpecification\CreateValueObject(),
            Specification\EnumSpecification::class => new CreateFromSpecification\CreateEnum(),
            Specification\ClassFunctionSpecification::class => new CreateFromSpecification\CreateClassFunction(),
            Specification\UnitTestSpecification::class => new CreateFromSpecification\CreateUnitTest(),
        ];

        /** @var array<class-string, ClassFile> $classFiles */
        $classFiles = [];

        foreach ($this->specifications as $fullClassName => $specification) {
            $classFile = new ClassFile($fullClassName);

            if (isset($cache[$classFile->fullClassName]) && $cache[$classFile->fullClassName] === $specification->hash() && $this->fileStorage->fileExists($classFile)) {
                echo 'skipped specification: ', $classFile->fullClassName, \PHP_EOL;
                continue;
            }

            if ($this->fileStorage->fileExists($classFile)) {
                $classFile = $this->fileStorage->getClassFileFromExistingFile($classFile->fullClassName);
            }

            $classFiles[$classFile->fullClassName] = $classFile;
            $cache[$classFile->fullClassName] = $specification->hash();

            $creator = $creators[$specification::class];
            $creator($classFile, $specification);
        }

        #\file_put_contents($this->cacheFile, \json_encode($cache, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT));

        $postProcessor = new PostProcessor();

        foreach ($this->specifications->getPostProcessorSpecifications() as $specification) {
            $classFiles[$specification->className] ??= $this->fileStorage->getClassFileFromExistingFile($specification->className);
            $postProcessor($classFiles[$specification->className], $specification);
        }

        foreach ($classFiles as $classFile) {
            $this->fileStorage->storeFile($classFile);
        }
    }
}
