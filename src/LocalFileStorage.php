<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

final readonly class LocalFileStorage implements FileStorage
{
    private PsrPrinter $printer;

    public function __construct(
        /** @var array<string, string> */
        private array $namespaceDirMapping,
    ) {
        $this->printer = new PsrPrinter();
    }

    public function getClassFileFromExistingFile(string $fullClassName): ClassFile
    {
        $fileKey = $this->getFileKeyForFullClassName($fullClassName);

        return ClassFile::fromPhpFile(PhpFile::fromCode(\file_get_contents($fileKey)));
    }

    public function fileExists(ClassFile $classFile): bool
    {
        $fileKey = $this->getFileKeyForFullClassName($classFile->fullClassName);

        return \is_file($fileKey);
    }

    public function storeFile(ClassFile $classFile): void
    {
        $fileKey = $this->getFileKeyForFullClassName($classFile->fullClassName);
        $this->ensureFileDirExists($fileKey);

        $file = $classFile->getFile();
        $file->setStrictTypes();

        \file_put_contents($fileKey, $this->printer->printFile($file));
    }

    private function getFileKeyForFullClassName(string $fullClassName): string
    {
        $rootNamespace = null;
        $rootSourceDir = null;

        foreach ($this->namespaceDirMapping as $namespace => $sourceDir) {
            if (\str_starts_with($fullClassName, $namespace)) {
                $rootNamespace = $namespace;
                $rootSourceDir = $sourceDir;
                break;
            }
        }

        if (null === $rootNamespace || null === $rootSourceDir) {
            throw new \RuntimeException('namespace not mapped');
        }

        $fileKey = \str_replace($rootNamespace, '', $fullClassName);
        $fileKey = \str_replace('\\', \DIRECTORY_SEPARATOR, $fileKey);
        $fileKey = $rootSourceDir . '/' . \ltrim($fileKey, '/') . '.php';

        return $fileKey;
    }

    private function ensureFileDirExists(string $fileKey): void
    {
        $fileDir = \dirname($fileKey);

        if (!\is_dir($fileDir) && !\mkdir($fileDir, recursive: true) && !\is_dir($fileDir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $fileDir));
        }
    }
}
