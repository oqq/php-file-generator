<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Util;

use Nette\PhpGenerator\Method;

final readonly class Name
{
    public static function camelCaseName(string $name): string
    {
        return \lcfirst(\str_replace([' ', '_', '-'], '', \ucwords($name, ' _-')));
    }

    public static function snakeCaseName(string $name): string
    {
        return \strtolower(\preg_replace('/[A-Z]/', '_$0', \lcfirst($name)));
    }

    /**
     * @param class-string $fqcn
     * @return non-empty-string
     */
    public static function className(string $fullClassName): string
    {
        $parts = \explode('\\', $fullClassName);

        return \array_pop($parts);
    }

    public static function sortMethods(Method $left, Method $right): int
    {
        $top = ['__construct', 'setUp'];

        if (\in_array($left->getName(), $top, true)) {
            return -1;
        }

        if (\in_array($right->getName(), $top, true)) {
            return 1;
        }

        return 0;
    }
}
