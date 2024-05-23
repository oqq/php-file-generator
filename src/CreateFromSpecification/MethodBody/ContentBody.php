<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;

final readonly class ContentBody implements CreateMethodBody
{
    public function __construct(
        private string $body,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $method->setBody($this->body);
    }

    public function hash(): string
    {
        return \md5($this->body);
    }
}
