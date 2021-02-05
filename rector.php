<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Core\Configuration\Option;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfReturnToEarlyReturnRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\Privatization\Rector\Class_\ChangeReadOnlyVariableWithDefaultValueToConstantRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Privatization\Rector\Class_\RepeatedLiteralToClassConstantRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeLocalPropertyToPrivatePropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(
        Option::SETS,
        [
            SetList::DOCTRINE_CODE_QUALITY,
            SetList::CODING_STYLE,
            SetList::CODE_QUALITY,
            SetList::CODE_QUALITY_STRICT,
            SetList::DEAD_CODE,
            SetList::PRIVATIZATION,
            SetList::NAMING,
            SetList::PHPUNIT_CODE_QUALITY,
            SetList::SYMFONY_AUTOWIRE,
            SetList::PHP_70,
            SetList::PHP_71,
            SetList::PHP_72,
            SetList::EARLY_RETURN,
        ]
    );
    $parameters->set(
        Option::SKIP,
        [
            FinalizeClassesWithoutChildrenRector::class,
            ChangeReadOnlyVariableWithDefaultValueToConstantRector::class,
            AddSeeTestAnnotationRector::class,
            RepeatedLiteralToClassConstantRector::class,
            PrivatizeLocalOnlyMethodRector::class,
            RenameParamToMatchTypeRector::class,
            RenameVariableToMatchMethodCallReturnTypeRector::class,
            EncapsedStringsToSprintfRector::class,
            PrivatizeLocalPropertyToPrivatePropertyRector::class,
            ChangeOrIfReturnToEarlyReturnRector::class,
            PrivatizeLocalGetterToPropertyRector::class,
            ChangeAndIfToEarlyReturnRector::class,
            VarConstantCommentRector::class,
        ]
    );
    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/config',
            __DIR__ . '/src',
            __DIR__ . '/tests',
            __DIR__ . '/changelog-linker.php',
            __DIR__ . '/ecs.php',
            __DIR__ . '/rector.php',
        ]
    );
};
