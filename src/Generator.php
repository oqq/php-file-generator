<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

final readonly class Generator
{
    private const array DEFAULT_CREATORS = [
        Specification\ValueObjectSpecification::class => CreateFromSpecification\CreateValueObject::class,
        Specification\EnumSpecification::class => CreateFromSpecification\CreateEnum::class,
        Specification\ClassFunctionSpecification::class => CreateFromSpecification\CreateClassFunction::class,
        Specification\UnitTestSpecification::class => CreateFromSpecification\CreateUnitTest::class,
    ];

    private const array DEFAULT_POST_PROCESSORS = [
        Specification\ClassPostProcessorSpecification::class => ClassPostProcessor::class,
    ];

    public function __construct(
        private FileStorage $fileStorage,
        private Specifications $specifications,
        private string $cacheFile,
        /** @var array<class-string<Specification>, CreateFromSpecification> */
        private array $creators = [],
        /** @var array<class-string<PostProcessorSpecification>, PostProcessFromSpecification> */
        private array $postProcessors = [],
    ) {
    }

    public function generate(): void
    {
        $cacheContent = \is_file($this->cacheFile) ? \file_get_contents($this->cacheFile) : '{}';
        $cache = \json_decode($cacheContent, associative: true, flags: \JSON_THROW_ON_ERROR);

        $creators = \array_map(static fn (string $createFromSpecification): CreateFromSpecification => new $createFromSpecification(), self::DEFAULT_CREATORS);
        $creators += $this->creators;

        $postProcessors = \array_map(static fn (string $postProcessFromSpecification): PostProcessFromSpecification => new $postProcessFromSpecification(), self::DEFAULT_POST_PROCESSORS);
        $postProcessors += $this->postProcessors;

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

        foreach ($this->specifications->getPostProcessorSpecifications() as $specification) {
            $classFiles[$specification->className] ??= $this->fileStorage->getClassFileFromExistingFile($specification->className);

            $postProcessor = $postProcessors[$specification::class];
            $postProcessor($classFiles[$specification->className], $specification);
        }

        foreach ($classFiles as $classFile) {
            $this->fileStorage->storeFile($classFile);
        }
    }
}
