<?php
/**
 * Copyright (c) Big Cart Energy. All rights reserved.
 * SPDX-License-Identifier: OSL-3.0
 */
declare(strict_types=1);

namespace BigCartEnergy\Core\Test\Unit\Model;

use BigCartEnergy\Core\Model\Modules;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Module\PackageInfo;
use PHPUnit\Framework\TestCase;

class ModulesTest extends TestCase
{
    public function testHumanName(): void
    {
        self::assertSame('Catalog Completeness', Modules::humanName('BigCartEnergy_CatalogCompleteness'));
        self::assertSame('Core', Modules::humanName('BigCartEnergy_Core'));
    }

    public function testListsOnlyBigCartEnergyModulesWithVersions(): void
    {
        if (!interface_exists(ModuleListInterface::class)) {
            self::markTestSkipped('Magento framework not available (set BCE_MAGENTO_ROOT).');
        }
        $moduleList = $this->createMock(ModuleListInterface::class);
        $moduleList->method('getNames')->willReturn([
            'Magento_Catalog',
            'BigCartEnergy_CatalogCompleteness',
            'BigCartEnergy_Core',
            'Vendor_Other',
        ]);
        $packageInfo = $this->createMock(PackageInfo::class);
        $packageInfo->method('getPackageName')->willReturnMap([
            ['BigCartEnergy_CatalogCompleteness', 'bigcartenergy/module-catalog-completeness'],
            ['BigCartEnergy_Core', 'bigcartenergy/module-core'],
        ]);
        $packageInfo->method('getVersion')->willReturnMap([
            ['BigCartEnergy_CatalogCompleteness', ''],
            ['BigCartEnergy_Core', '1.2.0'],
        ]);
        $composer = $this->createMock(ComposerInformation::class);
        $composer->method('getInstalledMagentoPackages')->willReturn([
            'bigcartenergy/module-catalog-completeness' => [
                'name' => 'bigcartenergy/module-catalog-completeness',
                'type' => 'magento2-module',
                'version' => '1.0.3',
            ],
        ]);

        $installed = (new Modules($moduleList, $packageInfo, $composer))->getInstalled();

        self::assertSame(
            [
                ['module' => 'BigCartEnergy_CatalogCompleteness', 'name' => 'Catalog Completeness', 'package' => 'bigcartenergy/module-catalog-completeness', 'version' => '1.0.3'],
                ['module' => 'BigCartEnergy_Core', 'name' => 'Core', 'package' => 'bigcartenergy/module-core', 'version' => '1.2.0'],
            ],
            $installed
        );
    }
}
