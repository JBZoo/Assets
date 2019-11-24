<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\Assets;

use Countable;
use JBZoo\Assets\Asset\Asset;
use JBZoo\Utils\Arr;

/**
 * Class Collection
 *
 * @package JBZoo\Assets
 */
class Collection implements Countable
{
    /**
     * Holds registered assets.
     *
     * @var array
     */
    protected $assets = [];

    /**
     * Collection constructor.
     *
     * @param array $assets
     */
    public function __construct(array $assets = [])
    {
        $this->assets = $assets;
    }

    /**
     * Adds asset to collection.
     *
     * @param Asset $asset
     * @return $this
     */
    public function add(Asset $asset): self
    {
        $this->assets[$asset->getAlias()] = $asset;
        return $this;
    }

    /**
     * Gets asset from collection.
     *
     * @param $name
     * @return Asset|null
     */
    public function get($name): ?Asset
    {
        return Arr::key($name, $this->assets, true);
    }

    /**
     * @return array
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * Removes assets from collection.
     *
     * @param string|array $name
     * @return void
     */
    public function remove($name): void
    {
        $names = (array)$name;

        /** @noinspection SuspiciousLoopInspection */
        foreach ($names as $name) {
            if (Arr::key($name, $this->assets)) {
                unset($this->assets[$name]);
            }
        }
    }

    /**
     * Countable interface implementation.
     *
     * @return int
     */
    public function count(): ?int
    {
        return count($this->assets);
    }
}
