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

use JBZoo\Assets\Asset\Asset;
use JBZoo\Utils\Arr;

/**
 * Class Collection
 *
 * @package JBZoo\Assets
 */
class Collection implements \Countable
{
    /**
     * Holds registered assets.
     *
     * @var array
     */
    protected $_assets = [];

    /**
     * Collection constructor.
     *
     * @param array $assets
     */
    public function __construct(array $assets = [])
    {
        $this->_assets = $assets;
    }

    /**
     * Adds asset to collection.
     *
     * @param Asset $asset
     * @return $this
     */
    public function add(Asset $asset)
    {
        $this->_assets[$asset->getAlias()] = $asset;
        return $this;
    }

    /**
     * Gets asset from collection.
     *
     * @param $name
     * @return Asset|null
     */
    public function get($name)
    {
        return Arr::key($name, $this->_assets, true);
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->_assets;
    }

    /**
     * Removes assets from collection.
     *
     * @param string|array $name
     * @return void
     */
    public function remove($name)
    {
        $names = (array)$name;

        foreach ($names as $name) {
            if (Arr::key($name, $this->_assets)) {
                unset($this->_assets[$name]);
            }
        }
    }

    /**
     * Countable interface implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->_assets);
    }
}
