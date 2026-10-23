<?php
/**
 * Copyright (c) Big Cart Energy. All rights reserved.
 * SPDX-License-Identifier: OSL-3.0
 */
declare(strict_types=1);

namespace BigCartEnergy\Core\Model;

use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Module\PackageInfo;

/**
 * The installed Big Cart Energy modules with their versions, for the About section.
 */
class Modules
{
    public const PREFIX = 'BigCartEnergy_';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @var ComposerInformation
     */
    private $composerInformation;

    public function __construct(
        ModuleListInterface $moduleList,
        PackageInfo $packageInfo,
        ComposerInformation $composerInformation
    ) {
        $this->moduleList = $moduleList;
        $this->packageInfo = $packageInfo;
        $this->composerInformation = $composerInformation;
    }

    /**
     * @return array<int, array{module: string, name: string, package: string, version: string}>
     */
    public function getInstalled(): array
    {
        $installedPackages = $this->installedPackageVersions();
        $modules = [];
        foreach ($this->moduleList->getNames() as $moduleName) {
            if (strncmp($moduleName, self::PREFIX, strlen(self::PREFIX)) !== 0) {
                continue;
            }
            $package = (string)$this->packageInfo->getPackageName($moduleName);
            $version = (string)$this->packageInfo->getVersion($moduleName);
            if ($version === '' && $package !== '' && isset($installedPackages[$package])) {
                $version = $installedPackages[$package];
            }
            $modules[] = [
                'module' => $moduleName,
                'name' => self::humanName($moduleName),
                'package' => $package,
                'version' => $version,
            ];
        }
        usort($modules, function (array $a, array $b): int {
            return strcmp($a['name'], $b['name']);
        });
        return $modules;
    }

    /**
     * "BigCartEnergy_CatalogCompleteness" becomes "Catalog Completeness".
     */
    public static function humanName(string $moduleName): string
    {
        $short = substr($moduleName, strlen(self::PREFIX)) ?: $moduleName;
        $spaced = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', ' ', $short);
        return $spaced === null ? $short : $spaced;
    }

    /**
     * @return array<string, string> package name => version
     */
    private function installedPackageVersions(): array
    {
        try {
            $packages = $this->composerInformation->getInstalledMagentoPackages();
        } catch (\Throwable $e) {
            return [];
        }
        $versions = [];
        foreach ($packages as $name => $package) {
            if (is_array($package) && isset($package['version'])) {
                $versions[(string)($package['name'] ?? $name)] = (string)$package['version'];
            }
        }
        return $versions;
    }
}
