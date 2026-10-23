<?php
/**
 * Copyright (c) Big Cart Energy. All rights reserved.
 * SPDX-License-Identifier: OSL-3.0
 */
declare(strict_types=1);

namespace BigCartEnergy\Core\Test\Unit\ViewModel;

use BigCartEnergy\Core\ViewModel\Brand;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use PHPUnit\Framework\TestCase;

class BrandTest extends TestCase
{
    private function brand(): Brand
    {
        if (!class_exists(ComponentRegistrar::class)) {
            self::markTestSkipped('Magento framework not available (set BCE_MAGENTO_ROOT).');
        }
        $registrar = $this->createMock(ComponentRegistrar::class);
        $registrar->method('getPath')->willReturn(dirname(__DIR__, 3));
        $driver = $this->createMock(FileDriver::class);
        $driver->method('isExists')->willReturnCallback(function (string $path): bool {
            return is_file($path);
        });
        $driver->method('fileGetContents')->willReturnCallback(function (string $path): string {
            return (string)file_get_contents($path);
        });
        return new Brand($registrar, $driver);
    }

    public function testMarkIsInlineDecorativeAndTakesTheTextColor(): void
    {
        $svg = $this->brand()->getMarkSvg(16);
        self::assertStringStartsWith('<svg', $svg);
        self::assertTrue(strpos($svg, 'aria-hidden="true"') !== false);
        self::assertTrue(strpos($svg, 'class="bce-brand-mark bce-brand-mark--16"') !== false);
        self::assertTrue(strpos($svg, 'width="16" height="16"') !== false);
        self::assertTrue(strpos($svg, 'currentColor') !== false);
        self::assertFalse(strpos($svg, 'aria-label') !== false);
        self::assertFalse(strpos($svg, 'role="img"') !== false);
    }

    public function testOnlyTheThreeSizesExist(): void
    {
        $brand = $this->brand();
        self::assertTrue(strpos($brand->getMarkSvg(20), 'bce-brand-mark--20') !== false);
        self::assertTrue(strpos($brand->getMarkSvg(32), 'bce-brand-mark--32') !== false);
        self::assertTrue(strpos($brand->getMarkSvg(48), 'bce-brand-mark--16') !== false, 'unknown sizes fall back to 16');
        self::assertTrue(strpos($brand->getWheelieSvg(32), 'bce-brand-wheelie--32') !== false);
    }

    public function testLinksAndName(): void
    {
        $brand = $this->brand();
        self::assertSame('Big Cart Energy', $brand->getName());
        self::assertSame('https://bigcartenergy.com', $brand->getSiteUrl());
        self::assertStringStartsWith('https://bigcartenergy.com/', $brand->getToolsUrl());
        self::assertStringStartsWith('https://bigcartenergy.com/', $brand->getContactUrl());
    }

    public function testShippedAssetsAreTheBrandFilesUnchanged(): void
    {
        $dir = dirname(__DIR__, 3) . '/view/adminhtml/web/images/brand/';
        foreach (['wheelie-mark.svg', 'wheelie.svg', 'favicon.svg'] as $file) {
            self::assertFileExists($dir . $file);
            self::assertStringStartsWith('<svg', trim((string)file_get_contents($dir . $file)));
        }
        self::assertTrue(strpos((string)file_get_contents($dir . 'wheelie-mark.svg'), 'currentColor') !== false);
    }
}
