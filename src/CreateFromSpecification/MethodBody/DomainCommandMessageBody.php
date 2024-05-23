<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification\MethodBody;

use Nette\PhpGenerator\Literal;
use Oqq\PhpFileGenerator\Type;
use Nette\PhpGenerator\Method;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateMethodBody;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Specifications;
use Oqq\PhpFileGenerator\Util\Name;

final readonly class DomainCommandMessageBody implements CreateMethodBody
{
    public function __construct(
        /** @var class-string */
        private string $commandClassName,
        private Specifications $specifications,
    ) {
    }

    public function __invoke(Method $method): void
    {
        $messageClassName = \trim($method->getParameters()['message']->getType(), '\\');
        $specificationType = $this->getMessageType($messageClassName);
        $values = [];

        foreach ($specificationType->elements as $name => $elementType) {
            $values[] = new Literal('$message->?', [Name::camelCaseName($name)]);
        }

        $commandClassName = new Literal('\\' . $this->commandClassName);

        $method->addBody('$this->commandBus->dispatch(?::with(...?));', [$commandClassName, $values]);
    }

    public function hash(): string
    {
        return md5($this->commandClassName);
    }

    /**
     * @param class-string $messageClass
     */
    private function getMessageType(string $messageClass): Type\ShapeType
    {
        $specification = $this->specifications->getSpecificationFor($messageClass);

        if (false === $specification instanceof ValueObjectSpecification) {
            throw new \RuntimeException('not sure how to handle specification');
        }

        $specificationType = $specification->type;

        if (false === $specificationType instanceof Type\ShapeType) {
            throw new \RuntimeException('not sure how to handle type');
        }

        return $specificationType;
    }
}
