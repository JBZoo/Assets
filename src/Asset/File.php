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

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Exception;
use JBZoo\Assets\Filter\Filter;
use JBZoo\Utils\FS;
use JBZoo\Utils\Url;
use JBZoo\Utils\Arr;
use JBZoo\Path\Path;
use JBZoo\Less\Less;
use JBZoo\Assets\Filter\FilterManager;

/**
 * Class File
 * @package JBZoo\Assets\Asset
 */
class File extends Asset
{

    const ASSET_TYPE_FILE = 'file';

    /**
     * Allowed asset extensions.
     *
     * @var array
     */
    protected $_allowed = ['css', 'js', 'less'];

    /**
     * Get asset ext.
     *
     * @return string
     */
    public function getExt()
    {
        return FS::ext($this->_source);
    }

    /**
     * Load file by type.
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function load(array $filters = [])
    {
        $assetExt = $this->getExt();

        if (!Arr::in($assetExt, $this->_allowed)) {
            $allowed = implode(' ', $this->_allowed);
            throw new Exception(sprintf('Invalid asset ext "%s", allowed is "%s"', $assetExt, $allowed));
        }

        if (!self::isExternal($this->_source)) {
            list($assetExt, $path) = $this->_findSource();

            $fManager = new FilterManager();

            if (count($filters)) {
                foreach ($filters as $name) {
                    /** @var Filter $filter */
                    $filter = $fManager->get($name);
                    $filter->setAsset($this);
                    $path = $filter->process();
                }
            }

            return [$assetExt, $this->_timestamp($path)];
        }

        return [$assetExt, $this->_source];
    }


    /**
     * @param $path
     * @return null|string
     */
    public function getContent($path)
    {
        if (is_file($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    /**
     * Gets full source file path.
     *
     * @return string
     */
    public function getFullPath()
    {
        return FS::clean($this->_root . '/' . $this->_source, '/');
    }

    /**
     * Find source in variants.
     *
     * @return array
     */
    protected function _findSource()
    {
        $ext    = $this->getExt();
        $jbPath = Path::getInstance();
        $path   = $this->getFullPath();

        if ($jbPath->isVirtual($this->_source)) {
            $path          = $jbPath->get($this->_source);
            $this->_source = $jbPath->url($this->_source, false);
        }

        if ($ext === 'less') {
            $ext           = 'css';
            $path          = $this->_lessProcess($path);
            $this->_source = FS::getRelative($path, $this->_root);
        }

        return [$ext, $path];
    }

    /**
     * Check external source.
     *
     * @param string $source
     * @return bool
     */
    public static function isExternal($source)
    {
        if (strpos($source, '://') || preg_match('/^\/\//', $source)) {
            return true;
        }

        return false;
    }

    /**
     * Process compile less.
     *
     * @param string $path
     * @return string
     * @throws \JBZoo\Less\Exception
     */
    protected function _lessProcess($path)
    {
        $less = new Less((array)$this->_params);
        return $less->compile($path);
    }

    /**
     * Add timestamp.
     *
     * @param string $path
     * @return bool|string
     */
    protected function _timestamp($path)
    {
        if (FS::isFile($path)) {
            $relative = FS::getRelative($path, $this->_root, '/');
            return Url::root() . '/' . $relative . '?' . filemtime($path);
        }

        return false;
    }
}
