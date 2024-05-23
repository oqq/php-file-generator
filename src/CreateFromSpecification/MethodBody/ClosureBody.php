<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Closure;
use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;

final readonly class ClosureBody implements CreateMethodBody
{
    public function __construct(
        private Closure $closure,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $closure = \Nette\PhpGenerator\Closure::from($this->closure);
        $body = $closure->getBody();

        $method->setBody($body);
    }

    public function hash(): string
    {
        $closure = \Nette\PhpGenerator\Closure::from($this->closure);
        $body = $closure->getBody();

        return md5($body);
    }
}
