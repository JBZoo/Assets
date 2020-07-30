<?php

/**
 * JBZoo Toolbox - Assets
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\Assets;

use JBZoo\Assets\Asset\AbstractAsset;
use JBZoo\Data\Data;
use JBZoo\Utils\FS;

/**
 * Class Factory
 * @package JBZoo\Assets
 */
class Factory
{
    /**
     * @var array
     */
    protected $customTypes = [
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

    /**
     * @var Manager
     */
    protected $eManager;

    /**
     * Factory constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->eManager = $manager;
    }

    /**
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->eManager;
    }

    /**
     * Create asset instance.
     *
     * @param string       $alias
     * @param mixed        $source
     * @param string|array $dependencies
     * @param array        $options
     * @return AbstractAsset
     * @throws Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function create(string $alias, $source, $dependencies = [], array $options = []): AbstractAsset
    {
        $assetType = $options['type'] ?? '';

        if (isset($this->customTypes[$assetType])) {
            $assetType = $this->customTypes[$assetType];
        } elseif (is_callable($source)) {
            $assetType = 'Callback';
        } elseif (is_string($source)) {
            $ext = strtolower(FS::ext($source));

            if ($ext === 'js') {
                $assetType = 'JsFile';
            } elseif ($ext === 'css') {
                $assetType = 'CssFile';
            } elseif ($ext === 'less') {
                $assetType = 'LessFile';
            } elseif ($ext === 'jsx') {
                $assetType = 'JsxFile';
            }
        } elseif (is_array($source)) {
            $assetType = 'Collection';
        }

        $className = __NAMESPACE__ . '\\Asset\\' . $assetType;
        if (class_exists($className)) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $options = new Data($options);
            return new $className($this->getManager(), $alias, $source, $dependencies, $options);
        }

        throw new Exception('Undefined asset type: ' . print_r($source, true));
    }

    /**
     * @param string $type
     * @param string $className
     */
    public function registerType(string $type, string $className): void
    {
        $this->customTypes[$type] = $className;
    }
}
