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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    private CreateValueObject $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = new CreateValueObject();
    }

    #[DataProvider('getImportExamples')]
    public function testWillImportTypes(Type $type, string $expectedTypeImport): void
    {
        $classFile = new ClassFile('X\Some');
        $valueObjectSpecification = new ValueObjectSpecification('X\Some', $type);

        $this->creator->__invoke($classFile, $valueObjectSpecification);

        $printer = new PsrPrinter();
        $result = $printer->printFile($classFile->getFile());

        self::assertStringContainsString('use ' . $expectedTypeImport, $result);
    }

    public static function getImportExamples(): iterable
    {
        yield [
            new ShapeType(['x' => new InstanceOfType('Y\Alpha')]),
            'Y\Alpha',
        ];

        yield [
            new ShapeType(['x' => new NullableType(new InstanceOfType('Y\Alpha'))]),
            'Y\Alpha',
        ];

        yield [
            new ShapeType(['x' => new ListType(new InstanceOfType('Y\Alpha'))]),
            'Y\Alpha',
        ];
    }

    #[DataProvider('getAnnoationExamples')]
    public function testWillResolveAnnotations(Type $type, string $expectedTypeAnnotation): void
    {
        $classFile = new ClassFile('X\Some');
        $valueObjectSpecification = new ValueObjectSpecification('X\Some', $type);

        $this->creator->__invoke($classFile, $valueObjectSpecification);

        $printer = new PsrPrinter();
        $result = $printer->printFile($classFile->getFile());

        self::assertStringContainsString('@var ' . $expectedTypeAnnotation, $result);
    }

    public static function getAnnoationExamples(): iterable
    {
        yield [
            new ShapeType(['x' => new ListType(new InstanceOfType('Y\Alpha'))]),
            'list<Alpha>',
        ];

        yield [
            new ShapeType(['x' => new ListType(new NonEmptyStringType())]),
            'list<non-empty-string>',
        ];
    }
}
