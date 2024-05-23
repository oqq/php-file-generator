<?php

declare(strict_types=1);

namespace Oqq\PhpFileGenerator\CreateFromSpecification;

use Nette\PhpGenerator\Type as NetteType;
use Oqq\PhpFileGenerator\ClassFile;
use Oqq\PhpFileGenerator\CreateFromSpecification;
use Oqq\PhpFileGenerator\Specification\EnumSpecification;
use Oqq\PhpFileGenerator\Util\Name;

/**
 * @implements CreateFromSpecification<EnumSpecification>
 */
final readonly class CreateEnum implements CreateFromSpecification
{
    public function __invoke(ClassFile $classFile, EnumSpecification $specification): void
    {
        $enum = $classFile->getEnum();
        $enum->setType(NetteType::String);
        $enum->setCases([]);

        foreach ($specification->cases as $case) {
            $caseName = \ucfirst(Name::camelCaseName($case));
            $enum->addCase($caseName, $case);
        }
    }
}
