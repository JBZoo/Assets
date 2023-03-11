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

use Countable;
use JBZoo\Assets\Asset\AbstractAsset;

final class Collection implements \Countable
{
    /**
     * Holds registered assets.
     *
     * @var AbstractAsset[]
     */
    private array $assets = [];

    public function __construct(array $assets = [])
    {
        $this->assets = $assets;
    }

    /**
     * Adds asset to collection.
     *
     * @return $this
     */
    public function add(AbstractAsset $asset): self
    {
        $this->assets[$asset->getAlias()] = $asset;

        return $this;
    }

    /**
     * Gets asset from collection.
     */
    public function get(string $name): ?AbstractAsset
    {
        return $this->assets[$name] ?? null;
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * Removes assets from collection.
     *
     * @param array|string $name
     */
    public function remove($name): void
    {
        $names = (array)$name;

        /** @noinspection SuspiciousLoopInspection */
        foreach ($names as $name) {
            if (\array_key_exists($name, $this->assets)) {
                unset($this->assets[$name]);
            }
        }
    }

    /**
     * Countable interface implementation.
     */
    public function count(): int
    {
        return \count($this->assets);
    }
}
