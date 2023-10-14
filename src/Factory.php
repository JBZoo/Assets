<?php

/**
 * JBZoo Toolbox - Assets.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Assets
 */

declare(strict_types=1);

namespace JBZoo\Assets;

use JBZoo\Assets\Asset\AbstractAsset;
use JBZoo\Assets\Asset\Callback as AssetCallback;
use JBZoo\Assets\Asset\Collection as AssetCollection;
use JBZoo\Assets\Asset\CssFile;
use JBZoo\Assets\Asset\JsFile;
use JBZoo\Assets\Asset\JsxFile;
use JBZoo\Assets\Asset\LessFile;
use JBZoo\Data\Data;
use JBZoo\Utils\FS;

final class Factory
{
    private Manager $eManager;

    private array $customTypes = [
        AbstractAsset::TYPE_CSS_CODE   => 'CssCode',
        AbstractAsset::TYPE_CSS_FILE   => 'CssFile',
        AbstractAsset::TYPE_JS_CODE    => 'JsCode',
        AbstractAsset::TYPE_JS_FILE    => 'JsFile',
        AbstractAsset::TYPE_JSX_CODE   => 'JsxCode',
        AbstractAsset::TYPE_JSX_FILE   => 'JsxFile',
        AbstractAsset::TYPE_LESS_FILE  => 'LessFile',
        AbstractAsset::TYPE_CALLBACK   => 'Callback',
        AbstractAsset::TYPE_COLLECTION => 'Collection',
    ];

    public function __construct(Manager $manager)
    {
        $this->eManager = $manager;
    }

    public function getManager(): Manager
    {
        return $this->eManager;
    }

    /**
     * Create asset instance.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
     * @suppress PhanUndeclaredClass
     */
    public function create(
        string $alias,
        array|\Closure|string $source,
        array|string $dependencies = [],
        array $options = [],
    ): AbstractAsset {
        $assetType = $options['type'] ?? '';

        if (isset($this->customTypes[$assetType])) {
            $assetType = $this->customTypes[$assetType];
        } elseif (\is_callable($source)) {
            $assetType = AssetCallback::class;
        } elseif (\is_string($source)) {
            $ext = \strtolower(FS::ext($source));

            if ($ext === 'js') {
                $assetType = JsFile::class;
            } elseif ($ext === 'css') {
                $assetType = CssFile::class;
            } elseif ($ext === 'less') {
                $assetType = LessFile::class;
            } elseif ($ext === 'jsx') {
                $assetType = JsxFile::class;
            }
        } else {
            $assetType = AssetCollection::class;
        }

        $options = new Data($options);

        if (\class_exists($assetType)) {
            /** @var AbstractAsset $assetType */
            return new $assetType($this->getManager(), $alias, $source, $dependencies, $options);
        }

        $fallbackClassName = __NAMESPACE__ . '\\Asset\\' . $assetType;
        if (\class_exists($fallbackClassName)) {
            /** @var AbstractAsset $fallbackClassName */
            return new $fallbackClassName($this->getManager(), $alias, $source, $dependencies, $options);
        }

        throw new Exception('Undefined asset type: ' . $assetType . '; Source: ' . \print_r($source, true));
    }

    public function registerType(string $type, string $className): void
    {
        $this->customTypes[$type] = $className;
    }
}
