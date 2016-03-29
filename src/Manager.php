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
     * @var Path
     */
    protected $_path;

    /**
     * @var array
     */
    protected $_default = [
        'debug' => false,
        'less'  => [],
    ];

    /**
     * Manager constructor.
     *
     * @param Path  $path
     * @param array $params
     */
    public function __construct(Path $path, $params = [])
    {
        $this->_params = new Data(array_merge($this->_default, $params));

        $this->_path       = $path;
        $this->_factory    = new Factory($this);
        $this->_collection = new Collection();
    }

    /**
     * @return Data
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value)
    {
        $this->_params->set($key, $value);
    }

    /**
     * @return Path
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Adds a registered asset or a new asset to the queue.
     *
     * @param string       $alias
     * @param string|null  $source
     * @param string|array $dependencies
     * @param string|array $options
     * @return $this
     */
    public function add($alias, $source = null, $dependencies = [], $options = [])
    {
        if ($source !== null) {
            $asset = $this->_factory->create($alias, $source, $dependencies, $options);
            $this->_collection->add($asset);

        } else {
            $asset = $this->_collection->get($alias);
        }

        if ($asset) {
            $this->_queued[$alias] = true;
        }

        return $this;
    }

    /**
     * Removes an asset from the queue.
     *
     * @param string $alias
     * @return $this
     */
    public function remove($alias)
    {
        unset($this->_queued[$alias]);
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
     */
    public function register($alias, $source = null, $dependencies = [], $options = [])
    {
        $asset = $this->_factory->create($alias, $source, $dependencies, $options);
        $this->_collection->add($asset);

        return $this;
    }

    /**
     * Unregisters an asset from collection.
     *
     * @param string $alias
     * @return $this
     */
    public function unregister($alias)
    {
        $this->_collection->remove($alias);
        $this->remove($alias);
        return $this;
    }

    /**
     * Get asset collections.
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get asset collections.
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->_factory;
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
        foreach (array_keys($this->_queued) as $alias) {
            $this->_resolveDependencies($this->_collection->get($alias), $assets);
        }

        /** @var Asset $asset */
        $result = [
            Asset::TYPE_JS_FILE  => [],
            Asset::TYPE_JS_CODE  => [],
            Asset::TYPE_JSX_FILE => [],
            Asset::TYPE_JSX_CODE => [],
            Asset::TYPE_CSS_FILE => [],
            Asset::TYPE_CSS_CODE => [],
        ];
        foreach ($assets as $asset) {

            $source = $asset->load($filters);

            if (Asset::TYPE_CALLBACK === $source[0]) {
                continue;
            }

            if (Asset::TYPE_COLLECTION === $source[0]) {
                $source = $source[1];
            } else {
                $source = array($source);
            }

            foreach ($source as $sourceItem) {

                $type = $sourceItem[0];
                $src  = $sourceItem[1];

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
    protected function _resolveDependencies(Asset $asset, &$resolved = [], &$unresolved = [])
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

                if ($dep = $this->_collection->get($dependency)) {
                    $this->_resolveDependencies($dep, $resolved, $unresolved);
                }
            }
        }

        $resolved[$asset->getAlias()] = $asset;
        unset($unresolved[$asset->getAlias()]);

        return $resolved;
    }
}
