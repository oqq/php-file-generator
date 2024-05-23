<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\Method;

interface CreateMethodBody
{
    public function __invoke(Method $method): void;

    public function hash(): string;
}
