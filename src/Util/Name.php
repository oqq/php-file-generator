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

    public static function pascalCaseName(string $name): string
    {
        return \ucfirst(\str_replace([' ', '_', '-'], '', \ucwords($name, ' _-')));
    }

    public static function snakeCaseName(string $name): string
    {
        $name = self::camelCaseName($name);
        $name = \preg_replace('/(?<=\\w)(?=[A-Z])|(?<=[a-z])(?=[0-9])/', '_', $name);

        return \strtolower($name);
    }

    public static function kebabCaseName(string $name): string
    {
        $name = self::camelCaseName($name);
        $name = \preg_replace('/(?<=\\w)(?=[A-Z])|(?<=[a-z])(?=[0-9])/', '-', $name);

        return \strtolower($name);
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
