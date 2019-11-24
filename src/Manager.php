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

use JBZoo\Assets\Asset\Asset;
use JBZoo\Data\Data;
use JBZoo\Path\Path;
use JBZoo\Utils\Arr;

/**
 * Class Manager
 * @package JBZoo\Assets
 */
class Manager
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
        $this->params = new Data(array_merge($this->default, $params));

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
    public function setParam($key, $value): void
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
     * @param string       $alias
     * @param string|null  $source
     * @param string|array $dependencies
     * @param string|array $options
     * @return $this
     * @throws Exception
     */
    public function add($alias, $source = null, $dependencies = [], $options = []): self
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
    public function remove($alias): self
    {
        unset($this->queued[$alias]);
        return $this;
    }

    /**
     * Registers an asset.
     *
     * @param string       $alias
     * @param null|string  $source
     * @param array        $dependencies
     * @param string|array $options
     * @return $this
     * @throws Exception
     */
    public function register($alias, $source = null, $dependencies = [], $options = []): self
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
    public function unregister($alias): self
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
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function build(array $filters = []): array
    {
        $assets = [];
        foreach (array_keys($this->queued) as $alias) {
            $this->resolveDependencies($this->collection->get($alias), $assets);
        }

        /** @var Asset $asset */
        $result = [
            Asset::TYPE_JS_FILE  => [],
            Asset::TYPE_JS_CODE  => [],
            Asset::TYPE_JSX_FILE => [],
            Asset::TYPE_JSX_CODE => [],
            Asset::TYPE_CSS_FILE => [],
            Asset::TYPE_CSS_CODE => [],
            Asset::TYPE_CALLBACK => [],
        ];
        foreach ($assets as $asset) {

            $source = $asset->load($filters);

            if (Asset::TYPE_COLLECTION === $source[0]) {
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
     * @param Asset|null $asset
     * @param Asset[]    $resolved
     * @param Asset[]    $unresolved
     * @return Asset[]
     * @throws Exception
     */
    protected function resolveDependencies(Asset $asset, &$resolved = [], &$unresolved = []): array
    {
        $unresolved[$asset->getAlias()] = $asset;

        foreach ($asset->getDependencies() as $dependency) {
            if (!Arr::key($dependency, $resolved)) {

                if (isset($unresolved[$dependency])) {
                    throw new Exception(sprintf(
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
