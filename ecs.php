<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);

    $ecsConfig->sets([SetList::PSR_12]);
    $ecsConfig->parallel();
    $ecsConfig->skip([
        'PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer' => '~',
        __DIR__ . '/app/Console/Kernel.php',
    ]);

    $ecsConfig->rule(ArraySyntaxFixer::class);
    $ecsConfig->rule(NoUnusedImportsFixer::class);

};

