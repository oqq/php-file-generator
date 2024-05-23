<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Property;
use Nette\PhpGenerator\Type as NetteType;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification;
use Oqq\PhpFileGenerator\Specification\UnitTestSpecification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @implements CreateFromSpecification<UnitTestSpecification>
 */
final readonly class CreateUnitTest implements CreateFromSpecification
{
    /** @var array<class-string> */
    private const array IMPORTS = [
        CoversClass::class,
        Test::class,
        MockObject::class,
        TestCase::class,
    ];

    public function __invoke(ClassFile $classFile, UnitTestSpecification $specification): void
    {
        $classFile->addImport($specification->testedClassName);

        $namespace = $classFile->getNamespace();
        $testedClassName = $namespace->simplifyType($specification->testedClassName);

        foreach (self::IMPORTS as $import) {
            $classFile->addImport($import);
        }
        
        $class = $classFile->getClass();
        $class->setFinal();

        $class->setAttributes([]);
        $class->addAttribute(CoversClass::class, [new Literal($testedClassName . '::class')]);
        $class->setExtends(TestCase::class);

        $method = $class->addMethod('setUp', overwrite: true);
        $method->setProtected();
        $method->setReturnType(NetteType::Void);

        $method->setBody('parent::setUp();');
        $method->addBody('');
        $method->addBody('');

        $dependencies = [];
        foreach ($specification->mocks as $mock => $mockedClass) {
            $classFile->addImport($mockedClass);

            $dependency = new Literal('$this->?', [$mock]);
            $dependencies[] = $dependency;

            $property = $this->replaceProperty($class, $mock);
            $property->setType(NetteType::intersection($mockedClass, MockObject::class));

            $mockedClassType = new Literal($namespace->simplifyName($mockedClass));
            $method->addBody('? = $this->createMock(?::class);', [$dependency, $mockedClassType]);
        }

        $property = $this->replaceProperty($class, 'handler');
        $property->setType($specification->testedClassName);

        $method->addBody('$this->handler = ?;', [Literal::new($testedClassName, $dependencies)]);

        $this->createTestMethods($class, $specification->testMethods);
        $this->createTestMethodExample($class);
    }

    /**
     * @param array<non-empty-string, CreateMethodBody> $testMethods
     */
    private function createTestMethods(ClassType $class, array $testMethods): void
    {
        foreach($testMethods as $testMethodName => $createBody) {
            if ($class->hasMethod($testMethodName)) {
                continue;
            }

            $method = $class->addMethod($testMethodName);
            $method->addAttribute(Test::class);
            $method->setPublic();
            $method->setReturnType(NetteType::Void);

            $createBody($method);
        }
    }

    private function createTestMethodExample(ClassType $class): void
    {
        if (\count($class->getMethods()) > 1) {
            return;
        }

        $method = $class->addMethod('testWill');
        $method->addAttribute(Test::class);
        $method->setPublic();
        $method->setReturnType(NetteType::Void);

        $method->setBody('self::assertTrue(true);');
    }

    private function replaceProperty(ClassType $class, string $propertyName): Property
    {
        $class->removeProperty($propertyName);

        $property = $class->addProperty($propertyName);
        $property->setPrivate();

        return $property;
    }
}
