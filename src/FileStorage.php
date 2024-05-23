<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

interface FileStorage
{
    /**
     * @param class-string $fullClassName
     */
    public function getClassFileFromExistingFile(string $fullClassName): ClassFile;

    public function fileExists(ClassFile $classFile): bool;

    public function storeFile(ClassFile $classFile): void;
}
