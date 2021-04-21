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

declare(strict_types=1);

namespace JBZoo\Assets;

use JBZoo\Assets\Asset\AbstractAsset;
use JBZoo\Data\Data;
use JBZoo\Path\Path;
use JBZoo\Utils\Arr;

/**
 * Class Manager
 * @package JBZoo\Assets
 */
final class Manager
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $queued = [];

    /**
     * @var Path
     */
    protected $path;

    /**
     * @var Data
     */
    protected $params;

    /**
     * @var array
     */
    protected $default = [
        'debug'       => false,
        'strict_mode' => false,
        'less'        => [],
    ];

    /**
     * Manager constructor.
     *
     * @param Path  $path
     * @param array $params
     */
    public function __construct(Path $path, $params = [])
    {
        $this->params = new Data(\array_merge($this->default, $params));

        $this->path = $path;
        $this->factory = new Factory($this);
        $this->collection = new Collection();
    }

    /**
     * @return Data
     */
    public function getParams(): Data
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParam(string $key, $value): void
    {
        $this->params->set($key, $value);
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * Adds a registered asset or a new asset to the queue.
     *
     * @param string               $alias
     * @param string|callable|null $source
     * @param string|array         $dependencies
     * @param array                $options
     * @return $this
     * @throws Exception
     */
    public function add(string $alias, $source = null, $dependencies = [], array $options = []): self
    {
        if ($source !== null) {
            $asset = $this->factory->create($alias, $source, $dependencies, $options);
            $this->collection->add($asset);
        }

        $this->queued[$alias] = true;

        return $this;
    }

    /**
     * Removes an asset from the queue.
     *
     * @param string $alias
     * @return $this
     */
    public function remove(string $alias): self
    {
        unset($this->queued[$alias]);
        return $this;
    }

    /**
     * Registers an asset.
     *
     * @param string               $alias
     * @param string|callable|null $source
     * @param array|string         $dependencies
     * @param array                $options
     * @return $this
     * @throws Exception
     */
    public function register(string $alias, $source = null, $dependencies = [], array $options = []): self
    {
        $asset = $this->factory->create($alias, $source, $dependencies, $options);
        $this->collection->add($asset);

        return $this;
    }

    /**
     * Unregisters an asset from collection.
     *
     * @param string $alias
     * @return $this
     */
    public function unregister(string $alias): self
    {
        $this->collection->remove($alias);
        $this->remove($alias);
        return $this;
    }

    /**
     * Get asset collections.
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * Get asset collections.
     *
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Build assets.
     *
     * @return array
     * @throws Exception
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

            if (AbstractAsset::TYPE_COLLECTION === $source[0]) {
                $source = $source[1];
            } else {
                $source = [$source];
            }

            foreach ($source as $sourceItem) {
                [$type, $src] = $sourceItem;

                if ($src && !Arr::in($src, $result[$type])) {
                    $result[$type][] = $src;
                }
            }
        }

        return $result;
    }

    /**
     * Resolves asset dependencies.
     *
     * @param AbstractAsset|null $asset
     * @param AbstractAsset[]    $resolved
     * @param AbstractAsset[]    $unresolved
     * @return AbstractAsset[]
     * @throws Exception
     */
    protected function resolveDependencies(?AbstractAsset $asset, &$resolved = [], &$unresolved = []): array
    {
        if (!$asset) {
            return $resolved;
        }

        $unresolved[$asset->getAlias()] = $asset;

        foreach ($asset->getDependencies() as $dependency) {
            if (!\array_key_exists($dependency, $resolved)) {
                if (isset($unresolved[$dependency])) {
                    throw new Exception(\sprintf(
                        'Circular asset dependency "%s > %s" detected.',
                        $asset->getAlias(),
                        $dependency
                    ));
                }

                if ($dep = $this->collection->get($dependency)) {
                    $this->resolveDependencies($dep, $resolved, $unresolved);
                } else {
                    throw new Exception("Undefined depends: $dependency");
                }
            }
        }

        $resolved[$asset->getAlias()] = $asset;
        unset($unresolved[$asset->getAlias()]);

        return $resolved;
    }
}
