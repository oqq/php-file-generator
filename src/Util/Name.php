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
