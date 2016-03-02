<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Assets
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Assets
 * @author    Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace JBZoo\Assets;

use JBZoo\Data\Data;
use JBZoo\Path\Path;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;

/**
 * Class Factory
 *
 * @package JBZoo\Assets
 */
class Factory
{

    /**
     * Map of asset types.
     *
     * @var array
     */
    protected $_types = [
        'file' => 'JBZoo\Assets\FileAsset',
    ];

    /**
     * Path to root directory.
     *
     * @var string
     */
    protected $_root;

    /**
     * Default params.
     *
     * @var array
     */
    protected $_default = [
        'debug'      => false,
        'combine'    => false,
        'minify_css' => false,
        'minify_js'  => false,
    ];

    /**
     * Factory params.
     *
     * @var Data
     */
    protected $_params;

    /**
     * Factory constructor.
     * @param string $root
     * @param array $params
     */
    public function __construct($root, array $params = [])
    {
        Path::getInstance()->setRoot($root);

        $this->_root = $root;
        $params = array_merge($this->_default, $params);
        $this->_params = new Data($params);
    }

    /**
     * Create asset instance.
     *
     * @param string $name
     * @param string $source
     * @param string|array $dependencies
     * @param string|array $options
     * @throws \InvalidArgumentException
     * @return AssetInterface
     */
    public function create($name, $source, $dependencies = [], $options = [])
    {
        $dependencies = (array) $dependencies;
        $options = $this->_normalizeOptions($options);
        $type = $options['type'];

        if (Arr::key($type, $this->_types)) {
            $class = $this->_types[$type];
            return new $class($this->_root, $this->_params, $name, $source, $dependencies, $options);
        }

        throw new \InvalidArgumentException('Asset type does not exist or was not determined.');
    }

    /**
     * Gets the full root path.
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->_root;
    }

    /**
     * Data params.
     *
     * @return Data
     */
    public function params()
    {
        return $this->_params;
    }

    /**
     * Registers an asset type.
     *
     * @param string $name
     * @param string $class
     * @return $this
     */
    public function register($name, $class)
    {
        $this->_types[Str::low($name)] = $class;
        return $this;
    }

    /**
     * Normalize options.
     *
     * @param string|array $options
     * @return array
     */
    protected function _normalizeOptions($options = [])
    {
        if (is_string($options)) {
            $options = ['type' => $options];
        }

        if (!Arr::key('type', $options)) {
            $options['type'] = FileAsset::ASSET_TYPE_FILE;
        }

        $options['type'] = Str::low($options['type']);

        return $options;
    }
}
