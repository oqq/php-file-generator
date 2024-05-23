<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use ArrayObject;

final readonly class ConfigArrayBody implements CreateMethodBody
{
    public function __construct(
        private array $value,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $method->setBody('return ?;', [$this->value]);
    }

    public function hash(): string
    {
        return md5(\random_bytes(32));
    }
}
