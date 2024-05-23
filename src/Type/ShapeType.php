<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\Type;

use Oqq\PhpFileGenerator\Type;

/**
 * @template Tk of non-empty-string
 * @template Tv
 *
 * @implements Type<array<Tk, Type<Tv>>>
 */
final readonly class ShapeType implements Type
{
    /**
     * @param array<Tk, Type<Tv>> $elements
     */
    public function __construct(
        public array $elements,
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function getTypeHint(): string
    {
        return 'array';
    }

    public function getTypeAnnotation(): string
    {
        $nodes = [];

        foreach ($this->elements as $elementName => $type) {
            $nodes[] = $elementName . ($type->isOptional() ? '?' : '') . ': ' . $type->getTypeAnnotation();
        }

        return 'array{' . implode(', ', $nodes) . '}';
    }
}
