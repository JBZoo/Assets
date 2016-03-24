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

use JBZoo\Utils\Arr;

/**
 * Class Manager
 *
 * @package JBZoo\Assets
 */
class Manager
{
    /**
     * @var Factory
     */
    protected $_factory;

    /**
     * @var Collection
     */
    protected $_collection;

    /**
     * @var array
     */
    protected $_queued = [];

    /**
     * Manager constructor.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->_factory    = $factory;
        $this->_collection = new Collection();
    }

    /**
     * Adds a registered asset or a new asset to the queue.
     *
     * @param string       $name
     * @param string|null  $source
     * @param string|array $dependencies
     * @param string|array $options
     * @return $this
     */
    public function add($name, $source = null, $dependencies = [], $options = [])
    {
        if ($source !== null) {
            $asset = $this->_factory->create($name, $source, $dependencies, $options);
            $this->_collection->add($asset);
        } else {
            $asset = $this->_collection->get($name);
        }

        if ($asset) {
            $this->_queued[$name] = true;
        }

        return $this;
    }

    /**
     * Registers an asset.
     *
     * @param string       $name
     * @param null|string  $source
     * @param array        $dependencies
     * @param string|array $options
     * @return $this
     */
    public function register($name, $source = null, $dependencies = [], $options = [])
    {
        $this->_collection->add($this->_factory->create($name, $source, $dependencies, $options));
        return $this;
    }

    /**
     * Removes an asset from the queue.
     *
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->_queued[$name]);
        return $this;
    }

    /**
     * Unregisters an asset from collection.
     *
     * @param string $name
     * @return $this
     */
    public function unRegister($name)
    {
        $this->_collection->remove($name);
        $this->remove($name);
        return $this;
    }

    /**
     * Get asset collections.
     *
     * @return Collection
     */
    public function collection()
    {
        return $this->_collection;
    }

    /**
     * Resolves asset dependencies.
     *
     * @param AssetInterface|null $asset
     * @param AssetInterface[]    $resolved
     * @param AssetInterface[]    $unresolved
     * @return AssetInterface[]
     */
    public function resolveDependencies(AssetInterface $asset, &$resolved = [], &$unresolved = [])
    {
        $unresolved[$asset->getName()] = $asset;

        foreach ($asset->getDependencies() as $dependency) {
            if (!Arr::key($dependency, $resolved)) {

                if (isset($unresolved[$dependency])) {
                    throw new \RuntimeException(sprintf(
                        'Circular asset dependency "%s > %s" detected.',
                        $asset->getName(),
                        $dependency
                    ));
                }

                if ($dep = $this->_collection->get($dependency)) {
                    $this->resolveDependencies($dep, $resolved, $unresolved);
                }
            }
        }

        $resolved[$asset->getName()] = $asset;
        unset($unresolved[$asset->getName()]);

        return $resolved;
    }

    /**
     * Build assets.
     *
     * @param array $filters
     * @return array
     */
    public function build(array $filters = [])
    {
        $assets = [];
        $output = ['css' => [], 'js' => []];
        foreach (array_keys($this->_queued) as $name) {
            $this->resolveDependencies($this->_collection->get($name), $assets);
        }

        $collection = new Collection($assets);
        $assets     = $collection->getAssets();

        foreach ($assets as $i) {
            /** @var Asset $asset */
            $asset = $i;
            list($type, $source) = $asset->load($filters);

            if ($source && !Arr::in($source, (array)$output[$type])) {
                $output[$type][] = $source;
            }
        }

        return $output;
    }
}
