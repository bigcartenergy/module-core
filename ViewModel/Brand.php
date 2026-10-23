<?php
/**
 * Copyright (c) Big Cart Energy. All rights reserved.
 * SPDX-License-Identifier: OSL-3.0
 */
declare(strict_types=1);

namespace BigCartEnergy\Core\ViewModel;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * The brand, once: name, links and the inline marks every Big Cart Energy module renders.
 * The marks are decorative wherever text sits beside them, so they carry aria-hidden.
 */
class Brand implements ArgumentInterface
{
    public const NAME = 'Big Cart Energy';
    public const SITE_URL = 'https://bigcartenergy.com';
    public const TOOLS_URL = 'https://bigcartenergy.com/tools';
    public const CONTACT_URL = 'https://bigcartenergy.com/contact';

    public const MARK_SIZES = [16, 20, 32];

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * @var array<string, string>
     */
    private $svgCache = [];

    public function __construct(ComponentRegistrar $componentRegistrar, FileDriver $fileDriver)
    {
        $this->componentRegistrar = $componentRegistrar;
        $this->fileDriver = $fileDriver;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSiteUrl(): string
    {
        return self::SITE_URL;
    }

    public function getToolsUrl(): string
    {
        return self::TOOLS_URL;
    }

    public function getContactUrl(): string
    {
        return self::CONTACT_URL;
    }

    /**
     * The plain mark as inline SVG in the surrounding text color, decorative. Safe to echo: built from the
     * module's own asset, with the accessibility attributes rewritten.
     */
    public function getMarkSvg(int $size = 16): string
    {
        return $this->inlineSvg('wheelie-mark.svg', $size, 'bce-brand-mark');
    }

    /**
     * The mark with motion lines, for the larger placements (About section).
     */
    public function getWheelieSvg(int $size = 32): string
    {
        return $this->inlineSvg('wheelie.svg', $size, 'bce-brand-wheelie');
    }

    private function inlineSvg(string $file, int $size, string $class): string
    {
        $size = in_array($size, self::MARK_SIZES, true) ? $size : 16;
        $svg = $this->readAsset($file);
        if ($svg === '') {
            return '';
        }
        // Decorative: text always sits beside the mark.
        $svg = preg_replace('/\s(role|aria-label)="[^"]*"/', '', $svg) ?? $svg;
        $attributes = sprintf(
            ' class="%s %s--%d" width="%d" height="%d" aria-hidden="true" focusable="false"',
            $class,
            $class,
            $size,
            $size,
            $size
        );
        return preg_replace('/<svg\b/', '<svg' . $attributes, $svg, 1) ?? '';
    }

    /**
     * Read one of the brand assets shipped with this module. Only the three known file names are ever read.
     */
    private function readAsset(string $file): string
    {
        if (!in_array($file, ['wheelie-mark.svg', 'wheelie.svg', 'favicon.svg'], true)) {
            return '';
        }
        if (isset($this->svgCache[$file])) {
            return $this->svgCache[$file];
        }
        $svg = '';
        try {
            $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'BigCartEnergy_Core');
            if ($moduleDir) {
                $path = $moduleDir . '/view/adminhtml/web/images/brand/' . $file;
                if ($this->fileDriver->isExists($path)) {
                    $svg = trim((string)$this->fileDriver->fileGetContents($path));
                }
            }
        } catch (\Throwable $e) {
            $svg = '';
        }
        if (strncmp($svg, '<svg', 4) !== 0) {
            $svg = '';
        }
        $this->svgCache[$file] = $svg;
        return $svg;
    }
}
