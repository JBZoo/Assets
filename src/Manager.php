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
use JBZoo\Data\AbstractData;
use JBZoo\Data\Data;
use JBZoo\Path\Path;

final class Manager
{
    private Factory      $factory;
    private Collection   $collection;
    private array        $queued = [];
    private Path         $path;
    private AbstractData $params;

    private array $default = [
        'debug'       => false,
        'strict_mode' => false,
        'less'        => [],
    ];

    public function __construct(Path $path, array $params = [])
    {
        $this->params = new Data(\array_merge($this->default, $params));

        $this->path       = $path;
        $this->factory    = new Factory($this);
        $this->collection = new Collection();
    }

    public function getParams(): AbstractData
    {
        return $this->params;
    }

    public function setParam(string $key, mixed $value): void
    {
        $this->params = $this->params->set($key, $value);
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * Adds a registered asset or a new asset to the queue.
     */
    public function add(
        string $alias,
        null|array|\Closure|string $source = null,
        array|string $dependencies = [],
        array $options = [],
    ): self {
        if ($source !== null) {
            $asset = $this->factory->create($alias, $source, $dependencies, $options);
            $this->collection->add($asset);
        }

        $this->queued[$alias] = true;

        return $this;
    }

    /**
     * Removes an asset from the queue.
     */
    public function remove(string $alias): self
    {
        unset($this->queued[$alias]);

        return $this;
    }

    /**
     * Registers an asset.
     */
    public function register(
        string $alias,
        array|\Closure|string $source,
        array|string $dependencies = [],
        array $options = [],
    ): self {
        $asset = $this->factory->create($alias, $source, $dependencies, $options);
        $this->collection->add($asset);

        return $this;
    }

    /**
     * Unregisters an asset from collection.
     */
    public function unregister(string $alias): self
    {
        $this->collection->remove($alias);
        $this->remove($alias);

        return $this;
    }

    /**
     * Get asset collections.
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * Get asset collections.
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Build assets.
     * @suppress PhanPossiblyUndeclaredVariable
     */
    public function build(): array
    {
        $assets = [];

        foreach (\array_keys($this->queued) as $alias) {
            $this->resolveDependencies($this->collection->get($alias), $assets);
        }

        $result = [
            AbstractAsset::TYPE_JS_FILE  => [],
            AbstractAsset::TYPE_JS_CODE  => [],
            AbstractAsset::TYPE_JSX_FILE => [],
            AbstractAsset::TYPE_JSX_CODE => [],
            AbstractAsset::TYPE_CSS_FILE => [],
            AbstractAsset::TYPE_CSS_CODE => [],
            AbstractAsset::TYPE_CALLBACK => [],
        ];

        /** @var AbstractAsset $asset */
        foreach ($assets as $asset) {
            $source = $asset->load();

            if ($source[0] === AbstractAsset::TYPE_COLLECTION) {
                $source = $source[1];
            } else {
                $source = [$source];
            }

            foreach ($source as $sourceItem) {
                [$type, $src] = $sourceItem;

                if (
                    $src !== null
                    && $src !== ''
                    && $type !== null
                    && $type !== ''
                    && !\in_array($src, $result[$type], true)
                ) {
                    $result[$type][] = $src;
                }
            }
        }

        return $result;
    }

    /**
     * Resolves asset dependencies.
     * @param  AbstractAsset[] $resolved
     * @param  AbstractAsset[] $unresolved
     * @return AbstractAsset[]
     */
    private function resolveDependencies(?AbstractAsset $asset, array &$resolved = [], array &$unresolved = []): array
    {
        if ($asset === null) {
            return $resolved;
        }

        $unresolved[$asset->getAlias()] = $asset;

        foreach ($asset->getDependencies() as $dependency) {
            if (!\array_key_exists($dependency, $resolved)) {
                if (isset($unresolved[$dependency])) {
                    throw new Exception(
                        \sprintf(
                            'Circular asset dependency "%s > %s" detected.',
                            $asset->getAlias(),
                            $dependency,
                        ),
                    );
                }

                $dep = $this->collection->get($dependency);
                if ($dep !== null) {
                    $this->resolveDependencies($dep, $resolved, $unresolved);
                } else {
                    throw new Exception("Undefined depends: {$dependency}");
                }
            }
        }

        $resolved[$asset->getAlias()] = $asset;
        unset($unresolved[$asset->getAlias()]);

        return $resolved;
    }
}
