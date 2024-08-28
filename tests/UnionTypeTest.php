<?php

declare(strict_types=1);

use Nette\PhpGenerator\PsrPrinter;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification\CreateValueObject;
use Oqq\PhpFileGenerator\Specification\ValueObjectSpecification;
use Oqq\PhpFileGenerator\Type;
use Oqq\PhpFileGenerator\Type\InstanceOfType;
use Oqq\PhpFileGenerator\Type\ListType;
use Oqq\PhpFileGenerator\Type\NonEmptyStringType;
use Oqq\PhpFileGenerator\Type\NullableType;
use Oqq\PhpFileGenerator\Type\ShapeType;
use Oqq\PhpFileGenerator\TypeBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UnionTypeTest extends TestCase
{
    private CreateValueObject $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = new CreateValueObject();
    }

    public function testWillCreateUnionType(): void
    {
        $type = TypeBuilder::union(TypeBuilder::nonEmptyString(), TypeBuilder::list(TypeBuilder::boolean()));

        $classFile = new ClassFile('X\Some');
        $valueObjectSpecification = new ValueObjectSpecification('X\Some', $type);

        $this->creator->__invoke($classFile, $valueObjectSpecification);

        $printer = new PsrPrinter();
        $result = $printer->printFile($classFile->getFile());

        self::assertStringContainsString('public string|array $value', $result);
        self::assertStringContainsString('@var non-empty-string|list<bool>', $result);
    }
}
