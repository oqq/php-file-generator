<?php

namespace Oqq\PhpFileGenerator;

use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;

final class PerPrinter extends PsrPrinter
{
    public function printMethod(Method $method, ?PhpNamespace $namespace = null, bool $isInterface = false): string
    {
        $body = parent::printMethod($method, $namespace, $isInterface);

        if ($method->getBody() === '') {
            $body = \preg_replace("/\)\s{\n}/", ') {}', $body);
        }

        return $body;
    }
}
