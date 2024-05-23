<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;

final readonly class ReadModelInsertListenerBody implements CreateMethodBody
{
    public function __construct(
        /** @var array<non-empty-string, non-empty-string> */
        private array $mapping,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $values = \array_map(
            static fn (string $value): Literal => new Literal('$event->?', [$value]),
            $this->mapping,
        );

        $method->addBody('$this->readModel->insertValues(?);', [$values]);
    }

    public function hash(): string
    {
        return md5(\serialize($this->mapping));
    }
}
