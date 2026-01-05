<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
                ->withPaths([
                    __DIR__.'/',
                ])
                ->withSkip([
                    __DIR__.'/vendor',
                ])
                ->withPreparedSets(
                    codingStyle: true,
                    naming: true,
                    privatization: true,
                    rectorPreset: true
                )
                ->withPhpSets(php85: true)
                ->withPhpVersion(PhpVersion::PHP_85)
                ->withAttributesSets(symfony: true, doctrine: true)
                ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
                ->withSets(
                    [
                        LevelSetList::UP_TO_PHP_85,
                    ]
                )
                ->withRules(
                    [
                    ]
                )
                ->withTypeCoverageLevel(50)
                ->withDeadCodeLevel(50)
                ->withCodeQualityLevel(50)
;
